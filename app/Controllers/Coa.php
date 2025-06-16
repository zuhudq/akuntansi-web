<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel; // 1. Panggil Model-nya

class Coa extends BaseController
{
    public function index()
    {
        $coaModel = new CoaModel(); // 2. Buat instance dari model

        // 3. Buat sebuah array data untuk dikirim ke view
        $data = [
            'title' => 'Chart of Accounts', // Contoh mengirim judul halaman
            'page_title' => 'Daftar Akun (Chart of Accounts)',
            'accounts' => $coaModel->findAll() // Ambil semua data dari tabel dan masukkan ke key 'accounts'
        ];

        // 4. Kirim array $data ke view
        return view('coa/index', $data);
    }

    // ... (setelah function index() )

    public function new()
    {
        // Fungsi ini hanya menampilkan halaman form
        return view('coa/new');
    }

    public function create()
    {
        // 1. Mengambil data dari form yang disubmit
        $data = $this->request->getPost();

        // 2. Memanggil model untuk menyimpan data
        $coaModel = new CoaModel();
        $coaModel->save($data);

        // 3. Mengatur pesan flash untuk notifikasi
        // 4. Kembali ke halaman daftar akun
        return redirect()->to('/coa')->with('success', 'Data Akun berhasil ditambahkan!');
    }

    // ... (setelah function create() )

    public function edit($id)
    {
        $coaModel = new CoaModel();

        // Ambil data akun berdasarkan ID, lalu kirim ke view
        $data = [
            'account' => $coaModel->find($id)
        ];

        return view('coa/edit', $data);
    }

    public function update($id)
    {
        // 1. Ambil data dari form
        $data = $this->request->getPost();

        // 2. Panggil model untuk update data
        $coaModel = new CoaModel();
        // Method save() CI4 akan otomatis melakukan UPDATE jika ada Primary Key ($id)
        $coaModel->update($id, $data);

        // 3. Redirect dengan pesan sukses
        return redirect()->to('/coa')->with('success', 'Data Akun berhasil diubah!');
    }

    // ... (setelah function update() )

    public function delete($id)
    {
        $coaModel = new CoaModel();

        // Hapus data berdasarkan ID
        $coaModel->delete($id);

        // Redirect dengan pesan sukses
        return redirect()->to('/coa')->with('success', 'Data Akun berhasil dihapus!');
    }
}
