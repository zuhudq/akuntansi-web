<?= $this->extend('layout/template_pemimpin') // Saya asumsikan kita kembali ke template utama yang sudah lengkap 
?>

<?= $this->section('title') ?>
Executive Dashboard
<?= $this->endSection() ?>

<?= $this->section('page_styles') ?>
<style>
    .content-wrapper {
        background-color: #111827;
        /* Warna dasar sangat gelap */
    }

    .navbar-nav {
        color: #f5c518;
    }

    .card {
        background: #1f2937;
        /* Warna card sedikit lebih terang */
        color: #e5e7eb;
        border: 1px solid #374151;
        border-radius: 0.75rem;
        /* Sudut lebih membulat */
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .form-control {
        background-color: #4b5563;
        border-color: #6b7280;
        color: #f9fafb;
    }

    .btn-primary {
        background-color: #FFD700;
        border-color: #FFD700;
        font-weight: bold;
    }


    .content-header,
    .card-title {
        color: #f9fafb;
        /* Warna teks utama menjadi putih */
        font-weight: 600;
    }


    /* Mengubah warna Info Box menjadi lebih elegan */
    .small-box.bg-success {
        background: linear-gradient(45deg, #2a9d8f, #264653) !important;
        color: #ffffff !important;
    }

    .small-box.bg-danger {
        background: linear-gradient(45deg, #f4a261, #e76f51) !important;
        color: #ffffff !important;
    }

    .small-box.bg-info {
        background: linear-gradient(45deg, #e9c46a, #f4a261) !important;
        color: #212529 !important;
    }

    .small-box.bg-warning {
        background-color: #e9ecef !important;
        color: #212529 !important;
    }

    .small-box.bg-gradient-success {
        background: linear-gradient(to right, #2a9d8f, #a2d2ff) !important;
        color: #ffffff !important;
    }

    .small-box.bg-gradient-danger {
        background: linear-gradient(to right, #e76f51, #e9c46a) !important;
        color: #ffffff !important;
    }

    .small-box>.inner>h3,
    .small-box>.inner>p {
        color: inherit !important;
    }

    .kpi-box {
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        color: #ffffff !important;
    }

    .kpi-box .inner h3,
    .kpi-box .inner p {
        color: #ffffff !important;
    }

    .kpi-box-profit {
        background: linear-gradient(45deg, #8b5cf6, #d946ef) !important;
        /* Gradasi Ungu ke Pink */
    }

    .kpi-box-growth-positive {
        background: linear-gradient(45deg, #22c55e, #84cc16) !important;
        /* Gradasi Hijau Segar */
    }

    .kpi-box-growth-negative {
        background: linear-gradient(45deg, #ef4444, #f97316) !important;
        /* Gradasi Merah ke Oranye */
    }

    /* Mengubah warna header card grafik */
    .card-primary .card-header,
    .card-info .card-header {
        background-color: #264653;
        color: white;
    }

    .card-success .card-header {
        background-color: #2a9d8f;
        color: white;
    }

    .card-danger .card-header {
        background-color: #e76f51;
        color: white;
    }

    .sidebar-dark-primary {
        background: #111827;
        /* Samakan dengan background konten utama */
    }

    .nav-sidebar .nav-link {
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .nav-sidebar .nav-link.active,
    .nav-sidebar .nav-item:hover .nav-link {
        background-color: rgba(12, 8, 0, 0.86) !important;
        /* Aksen Emas/Amber saat hover/aktif */
        color: #f59e0b !important;
    }

    .brand-link {
        border-bottom: 1px solid #4b5563 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Executive Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Dashboard</h3>
    </div>
    <div class="card-body">
        <form action="/pemimpin/dashboard" method="get">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group"><label>Tanggal Mulai</label><input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label>Tanggal Selesai</label><input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group"><button type="submit" class="btn btn-primary">Terapkan</button></div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp <?= number_format($totalPendapatan) ?></h3>
                <p>Total Pendapatan (Periode)</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rp <?= number_format($totalBeban) ?></h3>
                <p>Total Beban (Periode)</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rp <?= number_format($labaRugiPeriode) ?></h3>
                <p>Laba / Rugi (Periode)</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $jumlahJurnal ?></h3>
                <p>Jumlah Transaksi (Periode)</p>
            </div>
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h3><?= esc($topIncomeSource['nama_akun']) ?></h3>
                <p>Sumber Pendapatan Terbesar (Rp <?= number_format($topIncomeSource['balance']) ?>)</p>
            </div>
            <div class="icon"><i class="fas fa-trophy"></i></div>
        </div>
    </div>
    <div class="col-lg-6 col-12">
        <div class="small-box bg-gradient-danger">
            <div class="inner">
                <h3><?= esc($topExpenseSource['nama_akun']) ?></h3>
                <p>Pos Beban Terbesar (Rp <?= number_format($topExpenseSource['balance']) ?>)</p>
            </div>
            <div class="icon"><i class="fas fa-chart-pie"></i></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-12">
        <div class="small-box kpi-box kpi-box-profit">
            <div class="inner">
                <h3><?= number_format($profitMargin, 2) ?><sup style="font-size: 20px">%</sup></h3>
                <p>Profit Margin (Periode)</p>
            </div>
            <div class="icon"><i class="fas fa-percentage"></i></div>
        </div>
    </div>
    <div class="col-lg-6 col-12">
        <div class="small-box kpi-box <?= ($revenueGrowth >= 0) ? 'kpi-box-growth-positive' : 'kpi-box-growth-negative' ?>">
            <div class="inner">
                <h3><?= ($revenueGrowth >= 0) ? '+' : '' ?><?= number_format($revenueGrowth, 2) ?><sup style="font-size: 20px">%</sup></h3>
                <p>Pertumbuhan Pendapatan (vs. Periode Sebelumnya)</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tren Pendapatan vs Beban (12 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
                <div class="chart"><canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Komposisi Pendapatan (dalam periode)</h3>
            </div>
            <div class="card-body">
                <?php if (!empty(json_decode($incomeData, true))) : ?><canvas id="incomeDoughnutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas><?php else: ?><p class="text-center text-muted mt-5">Tidak ada data pendapatan.</p><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Komposisi Beban (dalam periode)</h3>
            </div>
            <div class="card-body">
                <?php if (!empty(json_decode($expenseData, true))) : ?><canvas id="doughnutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas><?php else: ?><p class="text-center text-muted mt-5">Tidak ada data beban.</p><?php endif; ?>
            </div>
        </div>
    </div>

</div> ```
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tren Saldo Aset Utama (30 Hari Terakhir)</h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="pemimpinCashTrendChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
<script>
    // --- SCRIPT UNTUK GRAFIK GARIS (SEBELUMNYA BAR, SEKARANG LINE) ---
    new Chart(document.getElementById('lineChart').getContext('2d'), {
        type: 'line', // Tipe diubah menjadi 'line'
        data: {
            labels: JSON.parse('<?= $lineChartLabels ?? '[]' ?>'),
            datasets: [{
                    label: 'Pendapatan',
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    tension: 0.3, // Membuat garis sedikit melengkung
                    data: JSON.parse('<?= $lineChartIncome ?? '[]' ?>'),
                    fill: true
                },
                {
                    label: 'Beban',
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    tension: 0.3,
                    data: JSON.parse('<?= $lineChartExpense ?? '[]' ?>'),
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                labels: {
                    fontColor: "#e5e7eb"
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: "#e5e7eb",
                        beginAtZero: true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: "#e5e7eb"
                    }
                }]
            }
        }
    });

    // --- SCRIPT UNTUK GRAFIK DONAT PENDAPATAN ---
    var incomeLabelsData = <?= $incomeLabels ?? '[]' ?>;
    var incomeDataValues = <?= $incomeData ?? '[]' ?>;
    if (incomeDataValues.length > 0) {
        new Chart(document.getElementById('incomeDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: incomeLabelsData,
                datasets: [{
                    data: incomeDataValues,
                    // PALET WARNA HIJAU ELEGAN
                    backgroundColor: ['#10b981', '#14b8a6', '#5eead4', '#99f6e4']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'right',
                    labels: {
                        fontColor: "white"
                    }
                }
            }
        });
    }

    // --- SCRIPT UNTUK GRAFIK DONAT BEBAN ---
    var expenseLabelsData = <?= $expenseLabels ?? '[]' ?>;
    var expenseDataValues = <?= $expenseData ?? '[]' ?>;
    if (expenseDataValues.length > 0) {
        new Chart(document.getElementById('doughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: expenseLabelsData,
                datasets: [{
                    data: expenseDataValues,
                    // PALET WARNA MERAH ELEGAN
                    backgroundColor: ['#ef4444', '#f87171', '#fca5a5', '#fecaca']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'right',
                    labels: {
                        fontColor: "white"
                    }
                }
            }
        });
    }

    var cashTrendLabelsData = <?= $cashTrendLabels ?? '[]' ?>;
    var cashTrendDataValues = <?= $cashTrendData ?? '[]' ?>;

    if (cashTrendDataValues.length > 0) {
        var cashTrendChartCanvas = document.getElementById('pemimpinCashTrendChart').getContext('2d');
        new Chart(cashTrendChartCanvas, {
            type: 'line',
            data: {
                labels: cashTrendLabelsData,
                datasets: [{
                    label: 'Saldo Aset Utama',
                    data: cashTrendDataValues,
                    backgroundColor: 'rgba(6, 214, 160, 0.2)', // Warna hijau toska transparan
                    borderColor: 'rgba(6, 214, 160, 1)', // Warna hijau toska solid
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(6, 214, 160, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    labels: {
                        fontColor: "white"
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            fontColor: "#e5e7eb",
                            // Format angka dengan pemisah ribuan
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        gridLines: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontColor: "#e5e7eb"
                        },
                        gridLines: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }]
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>