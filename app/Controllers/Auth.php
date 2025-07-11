<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Auth extends BaseController
{
    public function register()
    {
        return view('auth/register');
    }

    public function processRegister()
    {
        $rules = [
            'nama_lengkap'     => 'required|min_length[3]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role'             => 'required|in_list[admin,supervisor,user]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $role = $this->request->getVar('role');
        $email = $this->request->getVar('email');
        $namaLengkap = $this->request->getVar('nama_lengkap');

        $data = [
            'nama_lengkap'  => $namaLengkap,
            'email'         => $email,
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'role'          => $role,
        ];

        if ($role == 'user') {
            $token = bin2hex(random_bytes(20));
            $data['status'] = 'pending_verification';
            $data['verification_token'] = $token;
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = getenv('email.SMTPHost');
                $mail->SMTPAuth   = true;
                $mail->Username   = getenv('email.SMTPUser');
                $mail->Password   = getenv('email.SMTPPass');
                $mail->SMTPSecure = getenv('email.SMTPCrypto');
                $mail->Port       = getenv('email.SMTPPort');
                $mail->setFrom(getenv('email.SMTPUser'), 'Akuntansi Web App');
                $mail->addAddress($email, $namaLengkap);
                $mail->isHTML(true);
                $verificationLink = base_url("/verify-email?token=$token");
                $mail->Subject = 'Aktivasi Akun Anda';
                $mail->Body    = "Halo $namaLengkap,<br><br>Terima kasih telah mendaftar. Silakan klik link berikut untuk mengaktifkan akun Anda:<br><a href='$verificationLink'>Aktivasi Akun</a>";
                $mail->send();

                $userModel->save($data);
                return redirect()->to('/login')->with('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk verifikasi.');
            } catch (Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Gagal mengirim email verifikasi. Pastikan konfigurasi sudah benar.');
            }
        } else { // Alur untuk admin/supervisor
            $data['status'] = 'pending_approval';
            $userModel->save($data);
            return redirect()->to('/login')->with('success', 'Pendaftaran berhasil! Akun Anda akan aktif setelah disetujui oleh admin.');
        }
    }

    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        // Pengecekan 1: User ada & Password cocok
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->to('/login')->with('error', 'Email atau Password salah.');
        }

        // Pengecekan 2: Status harus 'active'
        if ($user['status'] != 'active') {
            $errorMessage = 'Akun Anda belum aktif atau sedang diproses.';
            if ($user['status'] == 'pending_verification') {
                $errorMessage = 'Akun Anda belum aktif. Silakan cek email untuk verifikasi.';
            } elseif ($user['status'] == 'pending_approval') {
                $errorMessage = 'Akun Anda sedang menunggu persetujuan dari Admin.';
            } elseif ($user['status'] == 'rejected') {
                $errorMessage = 'Pendaftaran Anda telah ditolak oleh admin.';
            }
            return redirect()->to('/login')->with('error', $errorMessage);
        }

        $session = session();
        $sessionData = [
            'user_id'      => $user['id'],
            'nama_lengkap' => $user['nama_lengkap'],
            'email'        => $user['email'],
            'role'         => $user['role'],
            'isLoggedIn'   => TRUE
        ];
        $session->set($sessionData);

        if ($user['role'] === 'pemimpin') {
            // Jika rolenya pemimpin, arahkan ke dashboard eksekutif
            return redirect()->to('/pemimpin/dashboard');
        }

        // Jika bukan pemimpin, arahkan ke dashboard utama
        return redirect()->to('/')->with('success', 'Login berhasil! Selamat datang kembali.');
    }

    public function verifyEmail()
    {
        $token = $this->request->getGet('token');
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Token verifikasi tidak ditemukan.');
        }
        $userModel = new UserModel();
        $user = $userModel->where('verification_token', $token)->first();
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token verifikasi tidak valid atau sudah kedaluwarsa.');
        }
        $data = ['status' => 'active', 'verification_token' => null];
        $userModel->update($user['id'], $data);
        return redirect()->to('/login')->with('success', 'Verifikasi email berhasil! Akun Anda kini aktif.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
