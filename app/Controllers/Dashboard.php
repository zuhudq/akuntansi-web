<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\JurnalDetailModel;
use App\Models\JurnalHeaderModel;

class Dashboard extends BaseController
{
    // Ganti seluruh fungsi index() dengan kode di bawah ini
    public function index()
    {
        $coaModel = new \App\Models\CoaModel();
        $detailModel = new \App\Models\JurnalDetailModel();
        $headerModel = new \App\Models\JurnalHeaderModel();

        $calculateBalance = function ($accountId) use ($detailModel) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->where('id_akun', $accountId);
            $result = $builder->get()->getRow();
            return ($result->total_debit ?? 0) - ($result->total_kredit ?? 0);
        };

        $asetAccounts = $coaModel->where('kategori_akun', 'Aset')->findAll();
        $totalAset = 0;
        foreach ($asetAccounts as $account) {
            $totalAset += $calculateBalance($account['id_akun']);
        }

        $liabilitasAccounts = $coaModel->where('kategori_akun', 'Liabilitas')->findAll();
        $totalLiabilitas = 0;
        foreach ($liabilitasAccounts as $account) {
            $totalLiabilitas += $calculateBalance($account['id_akun']) * -1;
        }

        $ekuitasAccounts = $coaModel->where('kategori_akun', 'Ekuitas')->findAll();
        $totalEkuitas = 0;
        foreach ($ekuitasAccounts as $account) {
            $totalEkuitas += $calculateBalance($account['id_akun']) * -1;
        }

        $totalPendapatan = 0;
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        foreach ($pendapatanAccounts as $account) {
            $totalPendapatan += $calculateBalance($account['id_akun']) * -1;
        }

        $totalBeban = 0;
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        foreach ($bebanAccounts as $account) {
            $totalBeban += $calculateBalance($account['id_akun']);
        }
        $labaRugiBerjalan = $totalPendapatan - $totalBeban;

        $currentYear = date('Y');
        $monthlyIncome = [];
        $monthlyExpense = [];

        for ($month = 1; $month <= 12; $month++) {
            $builder = $detailModel->builder();
            $builder->select('SUM(kredit) as total');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->join('chart_of_accounts', 'chart_of_accounts.id_akun = jurnal_detail.id_akun');
            $builder->where('chart_of_accounts.kategori_akun', 'Pendapatan');
            $builder->where('YEAR(jurnal_header.tanggal_jurnal)', $currentYear);
            $builder->where('MONTH(jurnal_header.tanggal_jurnal)', $month);
            $row = $builder->get()->getRow();
            $monthlyIncome[] = $row ? (float)$row->total : 0;

            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->join('chart_of_accounts', 'chart_of_accounts.id_akun = jurnal_detail.id_akun');
            $builder->where('chart_of_accounts.kategori_akun', 'Beban');
            $builder->where('YEAR(jurnal_header.tanggal_jurnal)', $currentYear);
            $builder->where('MONTH(jurnal_header.tanggal_jurnal)', $month);
            $row = $builder->get()->getRow();
            $monthlyExpense[] = $row ? (float)$row->total : 0;
        }

        $data = [
            'totalAset' => $totalAset,
            'totalLiabilitasEkuitas' => $totalLiabilitas + $totalEkuitas + $labaRugiBerjalan,
            'jumlahAkun' => $coaModel->countAllResults(),
            'jumlahJurnal' => $headerModel->countAllResults(),
            'monthlyIncome' => json_encode($monthlyIncome),
            'monthlyExpense' => json_encode($monthlyExpense),
        ];

        return view('dashboard/index', $data);
    }
}
