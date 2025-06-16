<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        return view('auth/register');
    }

    public function processRegister()
    {
        // 1. Aturan Validasi
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role'         => 'required|in_list[admin,supervisor,user]',
        ];

        // 2. Jalankan Validasi
        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembali ke halaman register dengan error
            return redirect()->to('/register')->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Jika validasi berhasil, simpan data
        $userModel = new UserModel();
        $data = [
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
            'email'        => $this->request->getVar('email'),
            // HASH PASSWORD SEBELUM DISIMPAN! JANGAN PERNAH SIMPAN PASSWORD ASLI.
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'role'         => $this->request->getVar('role'),
        ];
        $userModel->save($data);

        // 4. Redirect ke halaman login dengan pesan sukses
        return redirect()->to('/login')->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    // ... di dalam class Auth, setelah fungsi processRegister() ...

    public function login()
    {
        return view('auth/login');
    }

    // ... (di dalam class Auth) ...


    public function attemptLogin()
    {
        // 1. Ambil data dari form
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // 2. Cari user di database berdasarkan email
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        // 3. Lakukan Pengecekan
        // Jika user tidak ditemukan ATAU password tidak cocok
        if (!$user || !password_verify($password, $user['password_hash'])) {
            // Kembalikan ke halaman login dengan pesan error
            return redirect()->to('/login')->with('error', 'Email atau Password salah.');
        }

        // 4. Jika user ditemukan dan password cocok, buat sesi
        $session = session();
        $sessionData = [
            'user_id'       => $user['id'],
            'nama_lengkap'  => $user['nama_lengkap'],
            'email'         => $user['email'],
            'role'          => $user['role'],
            'isLoggedIn'    => TRUE // Penanda bahwa user sudah login
        ];
        $session->set($sessionData);

        // 5. Arahkan ke halaman utama aplikasi (dashboard)
        return redirect()->to('/')->with('success', 'Login berhasil! Selamat datang kembali.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
