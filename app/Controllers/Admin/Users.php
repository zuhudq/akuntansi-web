<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $data = [
            'page_title' => 'Manajemen Pengguna',
            'users' => $userModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('admin/users/index', $data);
    }

    public function new()
    {
        $data = [
            'page_title' => 'Tambah Pengguna Baru',
        ];
        return view('admin/users/new', $data);
    }

    public function create()
    {
        $rules = [
            'nama_lengkap'     => 'required|min_length[3]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role'             => 'required|in_list[admin,supervisor,user,pemimpin]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $email = $this->request->getVar('email');
        $namaLengkap = $this->request->getVar('nama_lengkap');
        $token = bin2hex(random_bytes(20));

        $data = [
            'nama_lengkap'      => $namaLengkap,
            'email'             => $email,
            'password_hash'     => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'role'              => $this->request->getVar('role'),
            'status'            => 'pending_verification',
            'verification_token' => $token,
        ];

        // Kirim email verifikasi menggunakan PHPMailer
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
            $mail->Subject = 'Akun Anda Telah Dibuatkan oleh Admin';
            $mail->Body    = "Halo $namaLengkap,<br><br>Sebuah akun telah dibuatkan untuk Anda oleh admin. Silakan klik link berikut untuk verifikasi email dan mengaktifkan akun Anda:<br><a href='$verificationLink'>Aktivasi Akun</a><br><br>Password Anda adalah: <b>" . esc($this->request->getVar('password')) . "</b><br>Harap segera ganti password Anda setelah berhasil login.";

            $mail->send();

            $userModel->save($data);
            return redirect()->to('/admin/users')->with('success', 'Pengguna baru berhasil dibuat dan email verifikasi telah dikirim.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Pengguna berhasil dibuat, namun email verifikasi gagal dikirim.');
        }
    }


    public function edit($id = null)
    {
        $userModel = new UserModel();
        $data = [
            'page_title' => 'Edit Pengguna',
            'user' => $userModel->find($id)
        ];
        if (!$data['user']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan.');
        }
        return view('admin/users/edit', $data);
    }

    public function update($id = null)
    {
        $userModel = new UserModel();
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => "required|valid_email|is_unique[users.email,id,$id]",
            'role'         => 'required|in_list[admin,supervisor,user,pemimpin]',
        ];
        if ($this->request->getVar('password')) {
            $rules['password'] = 'required|min_length[8]';
            $rules['password_confirm'] = 'required|matches[password]';
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = [
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'email'        => $this->request->getVar('email'),
            'role'         => $this->request->getVar('role'),
        ];
        if ($this->request->getVar('password')) {
            $data['password_hash'] = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
        }
        $userModel->update($id, $data);
        return redirect()->to('/admin/users')->with('success', 'Data pengguna berhasil di-update.');
    }

    public function delete($id = null)
    {
        $userModel = new UserModel();
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
    }
}
