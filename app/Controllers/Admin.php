<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Admin extends BaseController
{
    /**
     * Menampilkan halaman dengan daftar pengguna yang menunggu persetujuan.
     */
    public function pendingApprovals()
    {
        $userModel = new UserModel();

        $data = [
            'page_title' => 'Persetujuan Pengguna',
            'pendingUsers' => $userModel->where('status', 'pending_approval')->orderBy('created_at', 'DESC')->findAll()
        ];

        return view('admin/pending_approvals', $data);
    }

    /**
     * Menyetujui pendaftaran seorang pengguna berdasarkan ID.
     */
    public function approve($id)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);

        if (!$user || $user['status'] !== 'pending_approval') {
            return redirect()->to('/admin/pending-approvals')->with('error', 'User tidak valid atau sudah diproses.');
        }

        $token = bin2hex(random_bytes(20));
        $data = ['status' => 'pending_verification', 'verification_token' => $token];
        $userModel->update($id, $data);

        // Kirim email notifikasi menggunakan PHPMailer
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = getenv('email.SMTPHost');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('email.SMTPUser');
            $mail->Password   = getenv('email.SMTPPass');
            $mail->SMTPSecure = getenv('email.SMTPCrypto');
            $mail->Port       = getenv('email.SMTPPort');

            $mail->setFrom(getenv('email.SMTPUser'), 'Akun Disetujui');
            $mail->addAddress($user['email'], $user['nama_lengkap']);

            $mail->isHTML(true);
            $verificationLink = base_url("/verify-email?token=$token");
            $mail->Subject = 'Langkah Terakhir Aktivasi Akun Anda';
            $mail->Body    = 'Halo ' . esc($user['nama_lengkap']) . ',<br><br>Kabar baik! Pendaftaran Anda sebagai ' . esc($user['role']) . ' telah disetujui oleh Admin. Silakan klik link berikut untuk verifikasi email dan mengaktifkan akun Anda:<br><a href="' . $verificationLink . '">Aktivasi Akun Saya</a>';

            $mail->send();

            return redirect()->to('/admin/pending-approvals')->with('success', 'Pengguna ' . esc($user['nama_lengkap']) . ' berhasil disetujui dan email verifikasi telah dikirim.');
        } catch (\Exception $e) {
            return redirect()->to('/admin/pending-approvals')->with('error', 'User disetujui, namun email notifikasi gagal dikirim.');
        }
    }

    public function reject($id)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);

        if (!$user || $user['status'] !== 'pending_approval') {
            return redirect()->to('/admin/pending-approvals')->with('error', 'User tidak valid atau sudah diproses.');
        }

        // UBAH DARI DELETE MENJADI UPDATE STATUS
        $userModel->update($id, ['status' => 'rejected']);

        return redirect()->to('/admin/pending-approvals')->with('success', 'Pendaftaran untuk ' . esc($user['nama_lengkap']) . ' berhasil ditolak.');
    }
}
