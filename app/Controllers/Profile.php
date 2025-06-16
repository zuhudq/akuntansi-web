<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        // Hanya menampilkan halaman view
        return view('profile/index');
    }

    public function update()
    {
        $userModel = new UserModel();
        $userId = session()->get('user_id');

        // Aturan validasi
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
        ];

        // Aturan validasi kondisional untuk password
        if ($this->request->getVar('password')) {
            $rules['password'] = 'required|min_length[8]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Siapkan data untuk diupdate
        $data = [
            'id' => $userId,
            'nama_lengkap' => $this->request->getVar('nama_lengkap'),
        ];

        // Jika ada input password baru, hash dan tambahkan ke data
        if ($this->request->getVar('password')) {
            $data['password_hash'] = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
        }

        // Simpan perubahan ke database
        $userModel->save($data);

        // PENTING: Update juga data di session agar nama di sidebar ikut berubah
        session()->set('nama_lengkap', $data['nama_lengkap']);

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
