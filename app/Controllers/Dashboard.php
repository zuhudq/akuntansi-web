<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\JurnalDetailModel;
use App\Models\JurnalHeaderModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // 1. TANGKAP FILTER TANGGAL DARI URL. JIKA TIDAK ADA, GUNAKAN DEFAULT BULAN INI.
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();
        $headerModel = new JurnalHeaderModel();

        // --- FUNGSI HELPER UNTUK PERHITUNGAN ---

        // Fungsi untuk menghitung saldo akhir akun ASET, LIABILITAS, EKUITAS (posisi per tanggal akhir)
        $getNeracaBalance = function ($accountId, $saldoAwal, $endDate) use ($detailModel) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate); // Semua transaksi sampai tanggal akhir
            $movement = $builder->get()->getRow();
            $netMovement = ($movement->total_debit ?? 0) - ($movement->total_kredit ?? 0);
            // Saldo Akhir = Saldo Awal + Total Pergerakan
            return $saldoAwal + $netMovement;
        };

        // Fungsi untuk menghitung saldo akun LABA RUGI (hanya transaksi dalam periode)
        $getLabaRugiBalance = function ($accountId, $startDate, $endDate) use ($detailModel) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);
            $builder->where('jurnal_header.tanggal_jurnal >=', $startDate);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $result = $builder->get()->getRow();
            return ($result->total_debit ?? 0) - ($result->total_kredit ?? 0);
        };

        //--------------------------------------------------------------------
        // PERHITUNGAN DATA UNTUK INFO BOX (VERSI DINAMIS)
        //--------------------------------------------------------------------

        $totalAset = 0;
        $asetAccounts = $coaModel->where('kategori_akun', 'Aset')->findAll();
        foreach ($asetAccounts as $account) {
            $totalAset += $getNeracaBalance($account['id_akun'], $account['saldo_awal'], $endDate);
        }

        $totalLiabilitas = 0;
        $liabilitasAccounts = $coaModel->where('kategori_akun', 'Liabilitas')->findAll();
        foreach ($liabilitasAccounts as $account) {
            $totalLiabilitas += $getNeracaBalance($account['id_akun'], $account['saldo_awal'], $endDate) * -1;
        }

        $totalEkuitas = 0;
        $ekuitasAccounts = $coaModel->where('kategori_akun', 'Ekuitas')->findAll();
        foreach ($ekuitasAccounts as $account) {
            $totalEkuitas += $getNeracaBalance($account['id_akun'], $account['saldo_awal'], $endDate) * -1;
        }

        $totalPendapatan = 0;
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        foreach ($pendapatanAccounts as $account) {
            $totalPendapatan += $getLabaRugiBalance($account['id_akun'], $startDate, $endDate) * -1;
        }

        $totalBeban = 0;
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        foreach ($bebanAccounts as $account) {
            $totalBeban += $getLabaRugiBalance($account['id_akun'], $startDate, $endDate);
        }
        $labaRugiPeriode = $totalPendapatan - $totalBeban;

        //--------------------------------------------------------------------
        // PERHITUNGAN DATA UNTUK GRAFIK (VERSI DINAMIS)
        //--------------------------------------------------------------------

        $year = date('Y', strtotime($endDate));
        $monthlyIncome = [];
        $monthlyExpense = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyIncome[] = (float) $getLabaRugiBalance($pendapatanAccounts[0]['id_akun'] ?? 0, "$year-$month-01", "$year-$month-31") * -1;
            $monthlyExpense[] = (float) $getLabaRugiBalance($bebanAccounts[0]['id_akun'] ?? 0, "$year-$month-01", "$year-$month-31");
        }

        $expenseLabels = [];
        $expenseData = [];
        foreach ($bebanAccounts as $account) {
            $balance = $getLabaRugiBalance($account['id_akun'], $startDate, $endDate);
            if ($balance > 0) {
                $expenseLabels[] = $account['nama_akun'];
                $expenseData[] = (float)$balance;
            }
        }

        // Ambil 5 data jurnal terakhir
        $recentJournals = $headerModel->orderBy('id_jurnal', 'DESC')->limit(5)->findAll();

        // --- Siapkan semua data final untuk dikirim ke view ---
        $data = [
            'totalAset' => $totalAset,
            'totalLiabilitasEkuitas' => $totalLiabilitas + $totalEkuitas + $labaRugiPeriode,
            'jumlahAkun' => $coaModel->countAllResults(),
            'jumlahJurnal' => $headerModel->where('tanggal_jurnal >=', $startDate)->where('tanggal_jurnal <=', $endDate)->countAllResults(),
            'monthlyIncome' => json_encode($monthlyIncome),
            'monthlyExpense' => json_encode($monthlyExpense),
            'expenseLabels' => json_encode($expenseLabels),
            'expenseData' => json_encode($expenseData),
            'recentJournals' => $recentJournals,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('dashboard/index', $data);
    }
}
