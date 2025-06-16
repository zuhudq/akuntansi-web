<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\JurnalDetailModel;
use Dompdf\Dompdf;

class Laporan extends BaseController
{
    public function bukuBesar()
    {
        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();

        // 1. Ambil id_akun dari request GET (dari form filter)
        $selectedAccountId = $this->request->getGet('id_akun');

        // Siapkan variabel untuk menampung hasil
        $reportData = [];
        $selectedAccount = null;

        // 2. Jika ada akun yang dipilih, jalankan query
        if ($selectedAccountId) {
            // Ambil info akun yang dipilih
            $selectedAccount = $coaModel->find($selectedAccountId);

            // Query untuk mengambil detail jurnal
            $builder = $detailModel->builder();
            $builder->select('jurnal_detail.*, jurnal_header.tanggal_jurnal, jurnal_header.deskripsi');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $selectedAccountId);
            $builder->orderBy('jurnal_header.tanggal_jurnal', 'ASC'); // Penting: urutkan dari tanggal terlama
            $reportData = $builder->get()->getResultArray();
        }

        // 3. Siapkan semua data untuk dikirim ke view
        $data = [
            'accounts' => $coaModel->orderBy('kode_akun', 'ASC')->findAll(),
            'selectedAccountId' => $selectedAccountId, // Untuk menandai dropdown yang dipilih
            'selectedAccount' => $selectedAccount,     // Untuk menampilkan judul laporan
            'reportData' => $reportData,          // Hasil query laporan
        ];

        return view('laporan/buku_besar', $data);
    }

    // Di dalam file Laporan.php
    // ... setelah fungsi bukuBesar() ...
    public function labaRugi()
    {
        // Ambil tanggal dari form filter, jika tidak ada, gunakan default bulan ini
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();

        // 1. Ambil semua akun yang termasuk kategori Pendapatan
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        $pendapatanDetails = [];
        $totalPendapatan = 0;

        foreach ($pendapatanAccounts as $account) {
            $builder = $detailModel->builder();
            $builder->selectSum('kredit', 'total_kredit');
            $builder->selectSum('debit', 'total_debit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $account['id_akun']);
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $query = $builder->get()->getRow();

            $balance = $query->total_kredit - $query->total_debit; // Saldo normal pendapatan di kredit
            if ($balance > 0) {
                $pendapatanDetails[] = [
                    'nama_akun' => $account['nama_akun'],
                    'balance' => $balance,
                ];
                $totalPendapatan += $balance;
            }
        }

        // 2. Ambil semua akun yang termasuk kategori Beban
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        $bebanDetails = [];
        $totalBeban = 0;

        foreach ($bebanAccounts as $account) {
            $builder = $detailModel->builder();
            $builder->selectSum('debit', 'total_debit');
            $builder->selectSum('kredit', 'total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $account['id_akun']);
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $query = $builder->get()->getRow();

            $balance = $query->total_debit - $query->total_kredit; // Saldo normal beban di debit
            if ($balance > 0) {
                $bebanDetails[] = [
                    'nama_akun' => $account['nama_akun'],
                    'balance' => $balance,
                ];
                $totalBeban += $balance;
            }
        }

        // 3. Siapkan semua data untuk dikirim ke view
        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pendapatanDetails' => $pendapatanDetails,
            'totalPendapatan' => $totalPendapatan,
            'bebanDetails' => $bebanDetails,
            'totalBeban' => $totalBeban,
            'labaRugi' => $totalPendapatan - $totalBeban,
            'isFiltered' => ($this->request->getGet('start_date') !== null), // Penanda apakah form sudah disubmit
        ];

        return view('laporan/laba_rugi', $data);
    }

    public function cetakBukuBesar()
    {
        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();

        // Logika pengambilan datanya SAMA PERSIS dengan fungsi bukuBesar()
        $selectedAccountId = $this->request->getGet('id_akun');
        if (!$selectedAccountId) {
            return redirect()->to('/laporan/buku-besar')->with('error', 'Silakan pilih akun terlebih dahulu.');
        }

        $selectedAccount = $coaModel->find($selectedAccountId);
        $builder = $detailModel->builder();
        $builder->select('jurnal_detail.*, jurnal_header.tanggal_jurnal, jurnal_header.deskripsi');
        $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
        $builder->where('jurnal_detail.id_akun', $selectedAccountId);
        $builder->orderBy('jurnal_header.tanggal_jurnal', 'ASC');
        $reportData = $builder->get()->getResultArray();

        $data = [
            'selectedAccount' => $selectedAccount,
            'reportData' => $reportData,
        ];

        // Inisialisasi Dompdf
        $dompdf = new Dompdf();
        // Load view ke dalam Dompdf
        $dompdf->loadHtml(view('laporan/pdf_buku_besar', $data));
        // Setting ukuran dan orientasi kertas
        $dompdf->setPaper('A4', 'portrait');
        // Render HTML menjadi PDF
        $dompdf->render();
        // Output PDF ke browser
        // "Attachment" => 0 artinya tampilkan di browser, bukan langsung download
        $dompdf->stream("Laporan_Buku_Besar_" . time() . ".pdf", ["Attachment" => 0]);
    }

    // Di dalam file Laporan.php
    // ... setelah fungsi labaRugi() ...
    public function neraca()
    {
        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();

        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $isFiltered = ($this->request->getGet('end_date') !== null);

        // Fungsi helper untuk menghitung saldo satu akun
        $calculateBalance = function ($accountId, $endDate) use ($detailModel) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $result = $builder->get()->getRow();
            return $result->total_debit - $result->total_kredit;
        };

        // 1. HITUNG ASET
        $asetAccounts = $coaModel->where('kategori_akun', 'Aset')->orderBy('kode_akun', 'ASC')->findAll();
        $asetDetails = [];
        $totalAset = 0;
        foreach ($asetAccounts as $account) {
            $balance = $calculateBalance($account['id_akun'], $endDate);
            if ($balance != 0) { // Hanya tampilkan akun yang punya saldo
                $asetDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalAset += $balance;
            }
        }

        // 2. HITUNG LIABILITAS
        $liabilitasAccounts = $coaModel->where('kategori_akun', 'Liabilitas')->orderBy('kode_akun', 'ASC')->findAll();
        $liabilitasDetails = [];
        $totalLiabilitas = 0;
        foreach ($liabilitasAccounts as $account) {
            $balance = $calculateBalance($account['id_akun'], $endDate) * -1; // Saldo normal kredit
            if ($balance != 0) {
                $liabilitasDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalLiabilitas += $balance;
            }
        }

        // 3. HITUNG EKUITAS
        $ekuitasAccounts = $coaModel->where('kategori_akun', 'Ekuitas')->orderBy('kode_akun', 'ASC')->findAll();
        $ekuitasDetails = [];
        $totalEkuitas = 0;
        foreach ($ekuitasAccounts as $account) {
            $balance = $calculateBalance($account['id_akun'], $endDate) * -1; // Saldo normal kredit
            if ($balance != 0) {
                $ekuitasDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalEkuitas += $balance;
            }
        }

        // ===================================================================
        //     BAGIAN YANG DIPERBAIKI ADA DI SINI
        // ===================================================================
        // 4. HITUNG LABA/RUGI PERIODE BERJALAN (CARA YANG LEBIH AMAN)
        $totalPendapatan = 0;
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        foreach ($pendapatanAccounts as $account) {
            $totalPendapatan += $calculateBalance($account['id_akun'], $endDate) * -1;
        }

        $totalBeban = 0;
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        foreach ($bebanAccounts as $account) {
            $totalBeban += $calculateBalance($account['id_akun'], $endDate);
        }

        $labaRugiBerjalan = $totalPendapatan - $totalBeban;
        // ===================================================================
        //     AKHIR DARI BAGIAN YANG DIPERBAIKI
        // ===================================================================

        // Tambahkan Laba/Rugi ke Total Ekuitas
        $totalEkuitas += $labaRugiBerjalan;

        $data = [
            'endDate' => $endDate,
            'isFiltered' => $isFiltered,
            'asetDetails' => $asetDetails,
            'totalAset' => $totalAset,
            'liabilitasDetails' => $liabilitasDetails,
            'totalLiabilitas' => $totalLiabilitas,
            'ekuitasDetails' => $ekuitasDetails,
            'labaRugiBerjalan' => $labaRugiBerjalan,
            'totalEkuitas' => $totalEkuitas,
        ];

        return view('laporan/neraca', $data);
    }
}
