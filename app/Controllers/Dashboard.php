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
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();
        $headerModel = new JurnalHeaderModel();
        $getNeracaBalance = function ($accountId, $saldoAwal, $endDate) use ($detailModel) {
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $accountId);
            $builder->where('jurnal_header.tanggal_jurnal <=', $endDate);
            $movement = $builder->get()->getRow();
            $netMovement = ($movement->total_debit ?? 0) - ($movement->total_kredit ?? 0);
            return $saldoAwal + $netMovement;
        };

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

        $asetAccounts = $coaModel->where('kategori_akun', 'Aset')->findAll();
        $totalAset = 0;
        foreach ($asetAccounts as $account) {
            $totalAset += $getNeracaBalance($account['id_akun'], $account['saldo_awal'], $endDate);
        }
        $liabilitasAccounts = $coaModel->where('kategori_akun', 'Liabilitas')->findAll();
        $totalLiabilitas = 0;
        foreach ($liabilitasAccounts as $account) {
            $totalLiabilitas += $getNeracaBalance($account['id_akun'], $account['saldo_awal'], $endDate) * -1;
        }
        $ekuitasAccounts = $coaModel->where('kategori_akun', 'Ekuitas')->findAll();
        $totalEkuitas = 0;
        foreach ($ekuitasAccounts as $account) {
            $totalEkuitas += $getNeracaBalance($account['id_akun'], $account['saldo_awal'], $endDate) * -1;
        }
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        $totalPendapatan = 0;
        foreach ($pendapatanAccounts as $account) {
            $totalPendapatan += $getLabaRugiBalance($account['id_akun'], $startDate, $endDate) * -1;
        }
        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        $totalBeban = 0;
        foreach ($bebanAccounts as $account) {
            $totalBeban += $getLabaRugiBalance($account['id_akun'], $startDate, $endDate);
        }
        $labaRugiPeriode = $totalPendapatan - $totalBeban;
        $year = date('Y', strtotime($endDate));
        $monthlyIncome = [];
        $monthlyExpense = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStartDate = "$year-$month-01";
            $monthEndDate = date("Y-m-t", strtotime($monthStartDate));
            $totalMonthlyIncome = 0;
            if (!empty($pendapatanAccounts)) {
                foreach ($pendapatanAccounts as $account) {
                    $totalMonthlyIncome += $getLabaRugiBalance($account['id_akun'], $monthStartDate, $monthEndDate) * -1;
                }
            }
            $monthlyIncome[] = (float) $totalMonthlyIncome;
            $totalMonthlyExpense = 0;
            if (!empty($bebanAccounts)) {
                foreach ($bebanAccounts as $account) {
                    $totalMonthlyExpense += $getLabaRugiBalance($account['id_akun'], $monthStartDate, $monthEndDate);
                }
            }
            $monthlyExpense[] = (float) $totalMonthlyExpense;
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
        $incomeLabels = [];
        $incomeData = [];
        foreach ($pendapatanAccounts as $account) {
            $balance = $getLabaRugiBalance($account['id_akun'], $startDate, $endDate) * -1;
            if ($balance > 0) {
                $incomeLabels[] = $account['nama_akun'];
                $incomeData[] = (float)$balance;
            }
        }
        $recentJournals = $headerModel->orderBy('id_jurnal', 'DESC')->limit(5)->findAll();
        $cashTrendLabels = [];
        $cashTrendData = [];
        $targetAccountId = 15;
        $mainCashAccount = $coaModel->find($targetAccountId);
        $runningBalance = $mainCashAccount['saldo_awal'] ?? 0;
        $thirtyDaysAgo = date('Y-m-d', strtotime('-31 days'));
        $builder = $detailModel->builder();
        $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
        $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
        $builder->where('jurnal_detail.id_akun', $targetAccountId);
        $builder->where('jurnal_header.tanggal_jurnal <', $thirtyDaysAgo);
        $pastMovement = $builder->get()->getRow();
        $runningBalance += ($pastMovement->total_debit ?? 0) - ($pastMovement->total_kredit ?? 0);
        $builder = $detailModel->builder();
        $builder->select('jurnal_header.tanggal_jurnal, SUM(jurnal_detail.debit) as daily_debit, SUM(jurnal_detail.kredit) as daily_kredit');
        $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
        $builder->where('jurnal_detail.id_akun', $targetAccountId);
        $builder->where('jurnal_header.tanggal_jurnal >=', $thirtyDaysAgo);
        $builder->groupBy('jurnal_header.tanggal_jurnal');
        $builder->orderBy('jurnal_header.tanggal_jurnal', 'ASC');
        $dailyMovementsQuery = $builder->get()->getResultArray();
        $dailyMovements = [];
        foreach ($dailyMovementsQuery as $row) {
            $dailyMovements[$row['tanggal_jurnal']] = $row['daily_debit'] - $row['daily_kredit'];
        }

        for ($i = 30; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $cashTrendLabels[] = date('d M', strtotime($date));

            if (isset($dailyMovements[$date])) {
                $runningBalance += $dailyMovements[$date];
            }
            $cashTrendData[] = $runningBalance;
        }

        $data = [
            'totalAset' => $totalAset,
            'totalLiabilitasEkuitas' => $totalLiabilitas + $totalEkuitas + $labaRugiPeriode,
            'jumlahAkun' => $coaModel->countAllResults(),
            'jumlahJurnal' => $headerModel->where('tanggal_jurnal >=', $startDate)->where('tanggal_jurnal <=', $endDate)->countAllResults(),
            'monthlyIncome' => json_encode($monthlyIncome),
            'monthlyExpense' => json_encode($monthlyExpense),
            'expenseLabels' => json_encode($expenseLabels),
            'expenseData' => json_encode($expenseData),
            'incomeLabels' => json_encode($incomeLabels),
            'incomeData' => json_encode($incomeData),
            'recentJournals' => $recentJournals,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'cashTrendLabels' => json_encode($cashTrendLabels),
            'cashTrendData' => json_encode($cashTrendData),
        ];

        return view('dashboard/index', $data);
    }
}
