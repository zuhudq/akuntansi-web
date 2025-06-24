<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Dashboard</h3>
    </div>
    <div class="card-body">
        <form action="/" method="get">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group"><label>Tanggal Mulai</label><input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label>Tanggal Selesai</label><input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group"><button type="submit" class="btn btn-primary btn-block">Terapkan Filter</button></div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rp <?= number_format($totalAset, 0, ',', '.') ?></h3>
                <p>Total Aset (per <?= date('d M Y', strtotime($endDate)) ?>)</p>
            </div>
            <div class="icon"><i class="ion ion-bag"></i></div><a href="/laporan/neraca?end_date=<?= $endDate ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp <?= number_format($totalLiabilitasEkuitas, 0, ',', '.') ?></h3>
                <p>Total Liabilitas & Ekuitas</p>
            </div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div><a href="/laporan/neraca?end_date=<?= $endDate ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $jumlahAkun ?></h3>
                <p>Jumlah Akun (COA)</p>
            </div>
            <div class="icon"><i class="ion ion-document-text"></i></div><a href="/coa" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $jumlahJurnal ?></h3>
                <p>Transaksi (dalam periode)</p>
            </div>
            <div class="icon"><i class="ion ion-pie-graph"></i></div><a href="/jurnal" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Grafik Pendapatan vs Beban Tahun <?= date('Y', strtotime($endDate)) ?></h3>
            </div>
            <div class="card-body">
                <div class="chart"><canvas id="barChart" style="min-height: 535px; height: 535px; max-height: 535px; max-width: 100%;"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Komposisi Pendapatan (dalam periode)</h3>
            </div>
            <div class="card-body">
                <?php if (!empty(json_decode($incomeData, true))) : ?><canvas id="incomeDoughnutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                <?php else: ?><p class="text-center text-muted mt-5">Tidak ada data pendapatan untuk periode ini.</p><?php endif; ?>
            </div>
        </div>
        <div class="card card-danger mt-3">
            <div class="card-header">
                <h3 class="card-title">Komposisi Beban (dalam periode)</h3>
            </div>
            <div class="card-body">
                <?php if (!empty(json_decode($expenseData, true))) : ?><canvas id="doughnutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                <?php else: ?><p class="text-center text-muted mt-5">Tidak ada data beban untuk periode ini.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tren Saldo Aset Utama (30 Hari Terakhir)</h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="cashTrendChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aktivitas Jurnal Terakhir</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($recentJournals)): ?><li class="list-group-item">Belum ada transaksi.</li>
                        <?php else: ?><?php foreach ($recentJournals as $journal): ?><li class="list-group-item"><strong><?= date('d M Y', strtotime($journal['tanggal_jurnal'])) ?>:</strong> <?= esc($journal['deskripsi']) ?><a href="/jurnal/detail/<?= $journal['id_jurnal'] ?>" class="float-right btn btn-xs btn-outline-primary">Lihat Detail</a></li><?php endforeach; ?><?php endif; ?>
                </ul>
            </div>
            <div class="card-footer text-center"><a href="/jurnal">Lihat Semua Jurnal</a></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
<script>
    // --- SCRIPT UNTUK GRAFIK BATANG ---
    var monthlyIncomeData = <?= $monthlyIncome ?? '[]' ?>;
    var monthlyExpenseData = <?= $monthlyExpense ?? '[]' ?>;
    var barChartCanvas = document.getElementById('barChart').getContext('2d');
    var barChartData = {
        labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        datasets: [{
                label: 'Pendapatan',
                backgroundColor: '#007bff',
                data: monthlyIncomeData
            },
            {
                label: 'Beban',
                backgroundColor: '#6c757d',
                data: monthlyExpenseData
            }
        ]
    };
    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false
    };
    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    });

    // --- SCRIPT UNTUK GRAFIK DONAT PENDAPATAN ---
    var incomeLabelsData = <?= $incomeLabels ?? '[]' ?>;
    var incomeDataValues = <?= $incomeData ?? '[]' ?>;
    if (incomeDataValues.length > 0) {
        var incomeDoughnutCanvas = document.getElementById('incomeDoughnutChart').getContext('2d');
        var incomeDoughnutData = {
            labels: incomeLabelsData,
            datasets: [{
                data: incomeDataValues,
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#3c8dbc', '#6610f2', '#20c997'],
            }]
        };
        var doughnutOptions = {
            maintainAspectRatio: false,
            responsive: true
        };
        new Chart(incomeDoughnutCanvas, {
            type: 'doughnut',
            data: incomeDoughnutData,
            options: doughnutOptions
        });
    }

    // --- SCRIPT UNTUK GRAFIK DONAT BEBAN ---
    var expenseLabelsData = <?= $expenseLabels ?? '[]' ?>;
    var expenseDataValues = <?= $expenseData ?? '[]' ?>;
    if (expenseDataValues.length > 0) {
        var doughnutChartCanvas = document.getElementById('doughnutChart').getContext('2d');
        var doughnutData = {
            labels: expenseLabelsData,
            datasets: [{
                data: expenseDataValues,
                backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#17a2b8', '#6610f2', '#fd7e14'],
            }]
        };
        var doughnutOptions = {
            maintainAspectRatio: false,
            responsive: true,
        };
        new Chart(doughnutChartCanvas, {
            type: 'doughnut',
            data: doughnutData,
            options: doughnutOptions
        });
    }

    var cashTrendLabelsData = <?= $cashTrendLabels ?? '[]' ?>;
    var cashTrendDataValues = <?= $cashTrendData ?? '[]' ?>;
    var cashTrendChartCanvas = document.getElementById('cashTrendChart').getContext('2d');

    new Chart(cashTrendChartCanvas, {
        type: 'line',
        data: {
            labels: cashTrendLabelsData,
            datasets: [{
                label: 'Saldo Aset Utama',
                data: cashTrendDataValues,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(0, 123, 255, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false, // Saldo bisa negatif
                        callback: function(value, index, values) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }]
            }
        }
    });
</script>
<?= $this->endSection() ?>