<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\JurnalDetailModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $coaModel = new \App\Models\CoaModel();
        $detailModel = new \App\Models\JurnalDetailModel();
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $isFiltered = ($this->request->getGet('end_date') !== null);

        // Fungsi helper ini HANYA menghitung pergerakan (movement) transaksi pada periode tertentu
        $calculateMovement = function ($accountId, $endDate) use ($detailModel) {
            // Asumsi awal tahun fiskal adalah 1 Januari
            $startDate = date('Y-01-01', strtotime($endDate));

            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);

            // Kita hitung pergerakan dari awal tahun sampai tanggal yang dipilih
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $result = $builder->get()->getRow();

            return ($result->total_debit ?? 0) - ($result->total_kredit ?? 0);
        };

        $asetDetails = [];
        $totalAset = 0;
        $asetAccounts = $coaModel->where('kategori_akun', 'Aset')->orderBy('kode_akun', 'ASC')->findAll();
        foreach ($asetAccounts as $account) {
            $movement = $calculateMovement($account['id_akun'], $endDate);
            $balance = $account['saldo_awal'] + $movement;
            if ($balance != 0 || $account['saldo_awal'] != 0) {
                $asetDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalAset += $balance;
            }
        }

        $liabilitasDetails = [];
        $totalLiabilitas = 0;
        $liabilitasAccounts = $coaModel->where('kategori_akun', 'Liabilitas')->orderBy('kode_akun', 'ASC')->findAll();
        foreach ($liabilitasAccounts as $account) {
            $movement = $calculateMovement($account['id_akun'], $endDate);
            $balance = ($account['saldo_awal'] - $movement); // Saldo normal kredit jadi dibalik
            if ($balance != 0 || $account['saldo_awal'] != 0) {
                $liabilitasDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalLiabilitas += $balance;
            }
        }

        $ekuitasDetails = [];
        $totalEkuitas = 0;
        $ekuitasAccounts = $coaModel->where('kategori_akun', 'Ekuitas')->orderBy('kode_akun', 'ASC')->findAll();
        foreach ($ekuitasAccounts as $account) {
            $movement = $calculateMovement($account['id_akun'], $endDate);
            $balance = ($account['saldo_awal'] - $movement); // Saldo normal kredit jadi dibalik
            if ($balance != 0 || $account['saldo_awal'] != 0) {
                $ekuitasDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalEkuitas += $balance;
            }
        }

        // =============================================================
        //     INI BAGIAN PERHITUNGAN LABA/RUGI YANG SEBELUMNYA KELIRU
        // =============================================================
        $totalPendapatan = 0;
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        foreach ($pendapatanAccounts as $account) {
            // Saldo awal akun pendapatan/beban selalu 0 di awal tahun
            $movement = $calculateMovement($account['id_akun'], $endDate);
            $totalPendapatan += ($movement * -1); // Dibalik karena saldo normal kredit
        }

        $totalBeban = 0;
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        foreach ($bebanAccounts as $account) {
            $movement = $calculateMovement($account['id_akun'], $endDate);
            $totalBeban += $movement;
        }

        $labaRugiBerjalan = $totalPendapatan - $totalBeban;
        // =============================================================

        $data = [
            'endDate' => $endDate,
            'isFiltered' => $isFiltered,
            'asetDetails' => $asetDetails,
            'totalAset' => $totalAset,
            'liabilitasDetails' => $liabilitasDetails,
            'totalLiabilitas' => $totalLiabilitas,
            'ekuitasDetails' => $ekuitasDetails,
            'totalEkuitas' => $totalEkuitas, // Ini Total Ekuitas SEBELUM ditambah Laba/Rugi
            'labaRugiBerjalan' => $labaRugiBerjalan,
        ];

        return view('laporan/neraca', $data);
    }

    // ... di dalam class Laporan, setelah fungsi neraca() ...

    public function cetakLabaRugi()
    {
        // Ambil tanggal dari parameter GET
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $coaModel = new \App\Models\CoaModel();
        $detailModel = new \App\Models\JurnalDetailModel();

        // --- Logika perhitungan Laba Rugi (SAMA PERSIS seperti di fungsi labaRugi()) ---
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        $pendapatanDetails = [];
        $totalPendapatan = 0;
        foreach ($pendapatanAccounts as $account) {
            $builder = $detailModel->builder();
            $builder->select('SUM(kredit) - SUM(debit) as balance');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $account['id_akun']);
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $query = $builder->get()->getRow();
            $balance = $query->balance ?? 0;
            if ($balance > 0) {
                $pendapatanDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalPendapatan += $balance;
            }
        }
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        $bebanDetails = [];
        $totalBeban = 0;
        foreach ($bebanAccounts as $account) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) - SUM(kredit) as balance');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $account['id_akun']);
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $query = $builder->get()->getRow();
            $balance = $query->balance ?? 0;
            if ($balance > 0) {
                $bebanDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalBeban += $balance;
            }
        }
        // --- Akhir dari logika perhitungan ---

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pendapatanDetails' => $pendapatanDetails,
            'totalPendapatan' => $totalPendapatan,
            'bebanDetails' => $bebanDetails,
            'totalBeban' => $totalBeban,
            'labaRugi' => $totalPendapatan - $totalBeban,
        ];

        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        // Load view ke dalam Dompdf
        $dompdf->loadHtml(view('laporan/pdf_laba_rugi', $data));
        // Setting ukuran dan orientasi kertas
        $dompdf->setPaper('A4', 'portrait');
        // Render HTML menjadi PDF
        $dompdf->render();
        // Output PDF ke browser
        $dompdf->stream("Laporan_Laba_Rugi_" . date('d-m-Y') . ".pdf", ["Attachment" => 0]);
    }

    // ... di dalam class Laporan, setelah fungsi cetakLabaRugi() ...

    public function cetakNeraca()
    {
        // Logika pengambilan dan perhitungan data SAMA PERSIS dengan fungsi neraca()
        $coaModel = new \App\Models\CoaModel();
        $detailModel = new \App\Models\JurnalDetailModel();
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $calculateBalance = function ($accountId, $endDate) use ($detailModel) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $result = $builder->get()->getRow();
            return ($result->total_debit ?? 0) - ($result->total_kredit ?? 0);
        };

        $asetAccounts = $coaModel->where('kategori_akun', 'Aset')->orderBy('kode_akun', 'ASC')->findAll();
        $asetDetails = [];
        $totalAset = 0;
        foreach ($asetAccounts as $account) {
            $balance = $calculateBalance($account['id_akun'], $endDate);
            if ($balance != 0) {
                $asetDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalAset += $balance;
            }
        }

        $liabilitasAccounts = $coaModel->where('kategori_akun', 'Liabilitas')->orderBy('kode_akun', 'ASC')->findAll();
        $liabilitasDetails = [];
        $totalLiabilitas = 0;
        foreach ($liabilitasAccounts as $account) {
            $balance = $calculateBalance($account['id_akun'], $endDate) * -1;
            if ($balance != 0) {
                $liabilitasDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalLiabilitas += $balance;
            }
        }

        $ekuitasAccounts = $coaModel->where('kategori_akun', 'Ekuitas')->orderBy('kode_akun', 'ASC')->findAll();
        $ekuitasDetails = [];
        $totalEkuitas = 0;
        foreach ($ekuitasAccounts as $account) {
            $balance = $calculateBalance($account['id_akun'], $endDate) * -1;
            if ($balance != 0) {
                $ekuitasDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalEkuitas += $balance;
            }
        }

        $pendapatanBalance = 0;
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        foreach ($pendapatanAccounts as $account) {
            $pendapatanBalance += $calculateBalance($account['id_akun'], $endDate) * -1;
        }

        $bebanBalance = 0;
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        foreach ($bebanAccounts as $account) {
            $bebanBalance += $calculateBalance($account['id_akun'], $endDate);
        }
        $labaRugiBerjalan = $pendapatanBalance - $bebanBalance;
        $totalEkuitas += $labaRugiBerjalan;

        $data = [
            'endDate' => $endDate,
            'asetDetails' => $asetDetails,
            'totalAset' => $totalAset,
            'liabilitasDetails' => $liabilitasDetails,
            'totalLiabilitas' => $totalLiabilitas,
            'ekuitasDetails' => $ekuitasDetails,
            'labaRugiBerjalan' => $labaRugiBerjalan,
            'totalEkuitas' => $totalEkuitas,
        ];

        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('laporan/pdf_neraca', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan_Neraca_" . date('d-m-Y') . ".pdf", ["Attachment" => 0]);
    }

    // Di dalam class Laporan, setelah fungsi cetakNeraca()

    public function exportLabaRugiExcel()
    {
        // =======================================================
        //     BAGIAN YANG SEBELUMNYA HILANG, SEKARANG DILENGKAPI
        // =======================================================
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $coaModel = new \App\Models\CoaModel();
        $detailModel = new \App\Models\JurnalDetailModel();

        $getLabaRugiBalance = function ($accountId, $startDate, $endDate) use ($detailModel) {
            if (empty($startDate) || empty($endDate)) return 0;
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $result = $builder->get()->getRow();
            return ($result->total_debit ?? 0) - ($result->total_kredit ?? 0);
        };

        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        $totalPendapatan = 0;
        $pendapatanDetails = [];
        foreach ($pendapatanAccounts as $account) {
            $balance = $getLabaRugiBalance($account['id_akun'], $startDate, $endDate) * -1;
            if ($balance != 0) {
                $pendapatanDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalPendapatan += $balance;
            }
        }

        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        $totalBeban = 0;
        $bebanDetails = [];
        foreach ($bebanAccounts as $account) {
            $balance = $getLabaRugiBalance($account['id_akun'], $startDate, $endDate);
            if ($balance != 0) {
                $bebanDetails[] = ['nama_akun' => $account['nama_akun'], 'balance' => $balance];
                $totalBeban += $balance;
            }
        }
        $labaRugiPeriode = $totalPendapatan - $totalBeban;

        // =======================================================
        //     LOGIKA MENULIS KE EXCEL (TIDAK BERUBAH)
        // =======================================================
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menulis Header Laporan
        $sheet->setCellValue('A1', 'Laporan Laba Rugi');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($startDate)) . ' s/d ' . date('d M Y', strtotime($endDate)));
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Menulis Judul Kolom
        $sheet->setCellValue('A4', 'Keterangan');
        $sheet->setCellValue('B4', 'Jumlah');
        $sheet->getStyle('A4:B4')->getFont()->setBold(true);
        $sheet->getStyle('A4:B4')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Menulis Data
        $row = 5;
        $sheet->setCellValue('A' . $row, 'Pendapatan')->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        foreach ($pendapatanDetails as $item) {
            $sheet->setCellValue('A' . $row, $item['nama_akun'])->getStyle('A' . $row)->getAlignment()->setIndent(1);
            $sheet->setCellValue('B' . $row, $item['balance']);
            $row++;
        }
        $sheet->setCellValue('A' . $row, 'Total Pendapatan')->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $row, $totalPendapatan)->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;
        $row++;

        $sheet->setCellValue('A' . $row, 'Beban Operasional')->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        foreach ($bebanDetails as $item) {
            $sheet->setCellValue('A' . $row, $item['nama_akun'])->getStyle('A' . $row)->getAlignment()->setIndent(1);
            $sheet->setCellValue('B' . $row, $item['balance']);
            $row++;
        }
        $sheet->setCellValue('A' . $row, 'Total Beban')->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $row, $totalBeban)->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $row++;
        $row++;

        $sheet->setCellValue('A' . $row, 'LABA / (RUGI) BERSIH')->getStyle('A' . $row, 'B' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $row, $labaRugiPeriode)->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);

        // Mengatur format angka dan lebar kolom
        $sheet->getStyle('B5:B' . $row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        // Proses download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Laporan_Laba_Rugi_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit();
    }
}
