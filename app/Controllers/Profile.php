<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        return view('profile/index');
    }

    public function update()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);

        // Aturan validasi
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'avatar' => 'max_size[avatar,1024]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]'
        ];

        if ($this->request->getVar('password')) {
            $rules['password'] = 'required|min_length[8]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = ['id' => $userId, 'nama_lengkap' => $this->request->getVar('nama_lengkap')];
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile->isValid() && !$avatarFile->hasMoved()) {
            // Hapus avatar lama jika bukan avatar default
            if ($user['avatar'] && $user['avatar'] != 'default_avatar.png') {
                unlink('uploads/avatars/' . $user['avatar']);
            }
            $newName = $avatarFile->getRandomName();
            $avatarFile->move('uploads/avatars/', $newName);
            $data['avatar'] = $newName; // Simpan nama file baru ke database
        }

        if ($this->request->getVar('password')) {
            $data['password_hash'] = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
        }

        $userModel->save($data);
        session()->set('nama_lengkap', $data['nama_lengkap']);
        if (isset($data['avatar'])) {
            session()->set('avatar', $data['avatar']);
        }

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui!');
    }
    public function deleteAccount()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session()->get('user_id');

        // Hapus user dari database
        $userModel->delete($userId);

        // Hancurkan semua data sesi
        session()->destroy();

        // Arahkan ke halaman login dengan pesan perpisahan
        return redirect()->to('/login')->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}
