<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\JurnalDetailModel;
use App\Models\JurnalHeaderModel;

class Pemimpin extends BaseController
{
    public function dashboard()
    {
        // 1. Tangkap filter tanggal
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $isFiltered = ($this->request->getGet('start_date') !== null);

        // 2. Inisialisasi model
        $coaModel = new CoaModel();
        $detailModel = new JurnalDetailModel();
        $headerModel = new JurnalHeaderModel();

        // 3. Fungsi helper untuk perhitungan
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

        // 4. Perhitungan data KPI & Grafik
        $pendapatanAccounts = $coaModel->where('kategori_akun', 'Pendapatan')->findAll();
        $totalPendapatan = 0;
        $incomeDetails = [];
        foreach ($pendapatanAccounts as $account) {
            $balance = $getLabaRugiBalance($account['id_akun'], $startDate, $endDate) * -1;
            $totalPendapatan += $balance;
            if ($balance > 0) {
                $incomeDetails[$account['nama_akun']] = (float)$balance;
            }
        }

        $bebanAccounts = $coaModel->where('kategori_akun', 'Beban')->findAll();
        $totalBeban = 0;
        $expenseDetails = [];
        foreach ($bebanAccounts as $account) {
            $balance = $getLabaRugiBalance($account['id_akun'], $startDate, $endDate);
            $totalBeban += $balance;
            if ($balance > 0) {
                $expenseDetails[$account['nama_akun']] = (float)$balance;
            }
        }
        $labaRugiPeriode = $totalPendapatan - $totalBeban;

        $topIncomeSource = ['nama_akun' => 'N/A', 'balance' => 0];
        if (!empty($incomeDetails)) {
            arsort($incomeDetails);
            $topIncomeSource['nama_akun'] = key($incomeDetails);
            $topIncomeSource['balance'] = current($incomeDetails);
        }

        $topExpenseSource = ['nama_akun' => 'N/A', 'balance' => 0];
        if (!empty($expenseDetails)) {
            arsort($expenseDetails);
            $topExpenseSource['nama_akun'] = key($expenseDetails);
            $topExpenseSource['balance'] = current($expenseDetails);
        }

        $lineChartLabels = [];
        $lineChartIncome = [];
        $lineChartExpense = [];
        for ($i = 11; $i >= 0; $i--) {
            $targetDate = strtotime("-$i months", strtotime($endDate));
            $targetMonth = date("Y-m", $targetDate);
            $lineChartLabels[] = date("M Y", $targetDate);
            $monthStartDate = "$targetMonth-01";
            $monthEndDate = date("Y-m-t", strtotime($monthStartDate));
            $monthlyIncome = 0;
            if (!empty($pendapatanAccounts)) {
                foreach ($pendapatanAccounts as $account) {
                    $monthlyIncome += $getLabaRugiBalance($account['id_akun'], $monthStartDate, $monthEndDate) * -1;
                }
            }
            $lineChartIncome[] = (float) $monthlyIncome;
            $monthlyExpense = 0;
            if (!empty($bebanAccounts)) {
                foreach ($bebanAccounts as $account) {
                    $monthlyExpense += $getLabaRugiBalance($account['id_akun'], $monthStartDate, $monthEndDate);
                }
            }
            $lineChartExpense[] = (float) $monthlyExpense;
        }

        $profitMargin = ($totalPendapatan > 0) ? ($labaRugiPeriode / $totalPendapatan) * 100 : 0;

        $currentPeriodDuration = (new \DateTime($startDate))->diff(new \DateTime($endDate))->days;
        $previousStartDate = date('Y-m-d', strtotime("$startDate -" . ($currentPeriodDuration + 1) . " days"));
        $previousEndDate = date('Y-m-d', strtotime("$startDate -1 days"));

        $previousTotalPendapatan = 0;
        if (!empty($pendapatanAccounts)) {
            foreach ($pendapatanAccounts as $account) {
                $previousTotalPendapatan += $getLabaRugiBalance($account['id_akun'], $previousStartDate, $previousEndDate) * -1;
            }
        }

        $revenueGrowth = 0;
        if ($previousTotalPendapatan > 0) {
            $revenueGrowth = (($totalPendapatan - $previousTotalPendapatan) / $previousTotalPendapatan) * 100;
        } elseif ($totalPendapatan > 0) {
            $revenueGrowth = 100; // Pertumbuhan dianggap 100% jika sebelumnya 0
        }

        $cashTrendLabels = [];
        $cashTrendData = [];
        // PENTING: Ganti angka 1 ini dengan ID akun Kas/Bank utama di databasemu
        $targetAccountId = 15;

        $mainCashAccount = $coaModel->find($targetAccountId);
        if ($mainCashAccount) {
            // Ambil saldo awal dari COA
            $runningBalance = $mainCashAccount['saldo_awal'] ?? 0;

            // Hitung semua pergerakan SEBELUM 30 hari terakhir
            $thirtyDaysAgo = date('Y-m-d', strtotime('-31 days'));
            $builder = $detailModel->builder();
            $builder->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit');
            $builder->join('jurnal_header', 'jurnal_header.id_jurnal = jurnal_detail.id_jurnal');
            $builder->where('jurnal_detail.id_akun', $targetAccountId);
            $builder->where('jurnal_header.tanggal_jurnal <', $thirtyDaysAgo);
            $pastMovement = $builder->get()->getRow();
            $runningBalance += ($pastMovement->total_debit ?? 0) - ($pastMovement->total_kredit ?? 0);

            // Ambil pergerakan HARIAN selama 30 hari terakhir
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

            // Bangun data grafik hari per hari
            for ($i = 30; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $cashTrendLabels[] = date('d M', strtotime($date));
                if (isset($dailyMovements[$date])) {
                    $runningBalance += $dailyMovements[$date];
                }
                $cashTrendData[] = $runningBalance;
            }
        }



        $data = [
            'page_title' => 'Executive Dashboard',
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'labaRugiPeriode' => $labaRugiPeriode,
            'jumlahJurnal' => $headerModel->where('tanggal_jurnal >=', $startDate)->where('tanggal_jurnal <=', $endDate)->countAllResults(),
            'expenseLabels' => json_encode(array_keys($expenseDetails)),
            'expenseData' => json_encode(array_values($expenseDetails)),
            'incomeLabels' => json_encode(array_keys($incomeDetails)),
            'incomeData' => json_encode(array_values($incomeDetails)),
            'lineChartLabels' => json_encode($lineChartLabels),
            'lineChartIncome' => json_encode($lineChartIncome),
            'lineChartExpense' => json_encode($lineChartExpense),
            'topIncomeSource' => $topIncomeSource,
            'topExpenseSource' => $topExpenseSource,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isFiltered' => $isFiltered,
            'profitMargin' => $profitMargin,
            'revenueGrowth' => $revenueGrowth,
            'cashTrendLabels' => json_encode($cashTrendLabels),
            'cashTrendData' => json_encode($cashTrendData),
        ];

        return view('pemimpin/dashboard', $data);
    }
}
