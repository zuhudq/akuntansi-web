<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel; // PENTING: Panggil CoaModel untuk mengambil daftar akun
use App\Models\JurnalHeaderModel; // Pastikan baris ini ada
use App\Models\JurnalDetailModel;

class Jurnal extends BaseController
{

    public function index()
    {
        // Semua kode untuk tahap 1 diletakkan di sini
        $headerModel = new JurnalHeaderModel();
        $data = [
            'journals' => $headerModel->orderBy('tanggal_jurnal', 'DESC')->findAll(),
        ];
        return view('jurnal/index', $data);
    }

    // ... (setelah fungsi index() )

    public function detail($id)
    {
        $headerModel = new JurnalHeaderModel();
        $detailModel = new JurnalDetailModel();

        // 1. Ambil data header jurnal berdasarkan ID
        $journalHeader = $headerModel->find($id);

        // 2. Ambil data detail jurnal, gabungkan (JOIN) dengan tabel Akun (COA)
        // untuk mendapatkan nama dan kode akun
        $builder = $detailModel->builder();
        $builder->select('jurnal_detail.*, chart_of_accounts.nama_akun, chart_of_accounts.kode_akun');
        $builder->join('chart_of_accounts', 'chart_of_accounts.id_akun = jurnal_detail.id_akun');
        $builder->where('jurnal_detail.id_jurnal', $id);
        $journalDetails = $builder->get()->getResultArray();

        // Jika data tidak ditemukan, tampilkan error 404
        if (!$journalHeader) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jurnal tidak ditemukan.');
        }

        // 3. Kirim semua data ke view
        $data = [
            'journalHeader' => $journalHeader,
            'journalDetails' => $journalDetails,
        ];

        return view('jurnal/detail', $data);
    }

    // ... (setelah fungsi detail() )

    public function edit($id)
    {
        $headerModel = new JurnalHeaderModel();
        $detailModel = new JurnalDetailModel();
        $coaModel = new CoaModel();

        // Ambil data header
        $journalHeader = $headerModel->find($id);

        // Ambil data detail dengan JOIN
        $builder = $detailModel->builder();
        $builder->join('chart_of_accounts', 'chart_of_accounts.id_akun = jurnal_detail.id_akun');
        $builder->where('jurnal_detail.id_jurnal', $id);
        $journalDetails = $builder->get()->getResultArray();

        // Ambil semua akun untuk dropdown
        $allAccounts = $coaModel->orderBy('kode_akun', 'ASC')->findAll();

        if (!$journalHeader) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jurnal tidak ditemukan.');
        }

        $data = [
            'journalHeader' => $journalHeader,
            'journalDetails' => $journalDetails,
            'allAccounts' => $allAccounts
        ];

        return view('jurnal/edit', $data);
    }

    public function update($id)
    {
        $headerModel = new JurnalHeaderModel();
        $detailModel = new JurnalDetailModel();
        $postData = $this->request->getPost();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Update tabel header
            $headerData = [
                'tanggal_jurnal' => $postData['tanggal_jurnal'],
                'deskripsi'      => $postData['deskripsi'],
            ];
            $headerModel->update($id, $headerData);

            // 2. Hapus detail lama yang berhubungan dengan jurnal ini
            $detailModel->where('id_jurnal', $id)->delete();

            // 3. Insert detail baru (sama seperti fungsi create)
            $detailDebit = [
                'id_jurnal' => $id,
                'id_akun' => $postData['id_akun_debit'],
                'debit' => $postData['debit'],
                'kredit' => 0,
            ];
            $detailModel->save($detailDebit);

            $detailKredit = [
                'id_jurnal' => $id,
                'id_akun' => $postData['id_akun_kredit'],
                'debit' => 0,
                'kredit' => $postData['kredit'],
            ];
            $detailModel->save($detailKredit);

            $db->transCommit();
            return redirect()->to('/jurnal')->with('success', 'Jurnal berhasil diubah!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to("/jurnal/edit/$id")->with('error', 'Gagal mengubah jurnal: ' . $e->getMessage());
        }
    }

    // ... (setelah fungsi update() )

    public function delete($id)
    {
        $headerModel = new JurnalHeaderModel();

        // Cukup hapus data di header, detailnya akan ikut terhapus otomatis oleh database
        // berkat 'ON DELETE CASCADE' yang kita atur di migration.
        $headerModel->delete($id);

        return redirect()->to('/jurnal')->with('success', 'Jurnal berhasil dihapus!');
    }

    public function new()
    {
        $coaModel = new CoaModel();

        // Siapkan data untuk dikirim ke view
        $data = [
            'accounts' => $coaModel->findAll() // Kirim semua data akun untuk dropdown
        ];

        // Tampilkan view form
        return view('jurnal/new', $data);
    }

    public function create()
    {
        // Inisialisasi model
        $headerModel = new JurnalHeaderModel();
        $detailModel = new JurnalDetailModel();

        // Ambil data dari form
        $postData = $this->request->getPost();

        // Mulai database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Simpan ke tabel jurnal_header
            $headerData = [
                'tanggal_jurnal' => $postData['tanggal_jurnal'],
                'deskripsi' => $postData['deskripsi'],
            ];
            $headerModel->save($headerData);

            // 2. Dapatkan ID dari header yang baru saja disimpan
            $id_jurnal = $headerModel->getInsertID();

            // 3. Simpan baris Debit ke tabel jurnal_detail
            $detailDebit = [
                'id_jurnal' => $id_jurnal,
                'id_akun' => $postData['id_akun_debit'],
                'debit' => $postData['debit'],
                'kredit' => 0, // Sisi debit, kreditnya 0
            ];
            $detailModel->save($detailDebit);

            // 4. Simpan baris Kredit ke tabel jurnal_detail
            $detailKredit = [
                'id_jurnal' => $id_jurnal,
                'id_akun' => $postData['id_akun_kredit'],
                'debit' => 0, // Sisi kredit, debitnya 0
                'kredit' => $postData['kredit'],
            ];
            $detailModel->save($detailKredit);

            // Jika semua query berhasil, commit transaction
            $db->transCommit();

            // Set pesan sukses
            return redirect()->to('/jurnal')->with('success', 'Jurnal berhasil disimpan!');
        } catch (\Exception $e) {
            // Jika ada error, rollback transaction
            $db->transRollback();

            // Set pesan error
            return redirect()->to('/jurnal/new')->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }
}
