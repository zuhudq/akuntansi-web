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
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Terapkan Filter</button>
                    </div>
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
            <div class="icon"><i class="ion ion-bag"></i></div>
            <a href="/laporan/neraca?end_date=<?= $endDate ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp <?= number_format($totalLiabilitasEkuitas, 0, ',', '.') ?></h3>
                <p>Total Liabilitas & Ekuitas</p>
            </div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div>
            <a href="/laporan/neraca?end_date=<?= $endDate ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $jumlahAkun ?></h3>
                <p>Jumlah Akun (COA)</p>
            </div>
            <div class="icon"><i class="ion ion-document-text"></i></div>
            <a href="/coa" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $jumlahJurnal ?></h3>
                <p>Transaksi (dalam periode)</p>
            </div>
            <div class="icon"><i class="ion ion-pie-graph"></i></div>
            <a href="/jurnal" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Grafik Pendapatan vs Beban Tahun <?= date('Y', strtotime($endDate)) ?></h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Komposisi Beban (<?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (!empty(json_decode($expenseData))) : ?>
                    <canvas id="doughnutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                <?php else: ?>
                    <p class="text-center text-muted mt-5">Tidak ada data beban untuk ditampilkan pada periode ini.</p>
                <?php endif; ?>
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
                    <?php if (empty($recentJournals)): ?>
                        <li class="list-group-item">Belum ada transaksi.</li>
                    <?php else: ?>
                        <?php foreach ($recentJournals as $journal): ?>
                            <li class="list-group-item">
                                <strong><?= date('d M Y', strtotime($journal['tanggal_jurnal'])) ?>:</strong>
                                <?= esc($journal['deskripsi']) ?>
                                <a href="/jurnal/detail/<?= $journal['id_jurnal'] ?>" class="float-right btn btn-xs btn-outline-primary">Lihat Detail</a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="/jurnal">Lihat Semua Jurnal</a>
            </div>
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
            backgroundColor: 'rgba(60,141,188,0.9)',
            data: monthlyIncomeData
        }, {
            label: 'Beban',
            backgroundColor: 'rgba(210, 214, 222, 1)',
            data: monthlyExpenseData
        }]
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

    // --- SCRIPT UNTUK GRAFIK DONAT ---
    var expenseLabelsData = <?= $expenseLabels ?? '[]' ?>;
    var expenseDataValues = <?= $expenseData ?? '[]' ?>;

    if (expenseDataValues.length > 0) {
        var doughnutChartCanvas = document.getElementById('doughnutChart').getContext('2d');
        var doughnutData = {
            labels: expenseLabelsData,
            datasets: [{
                data: expenseDataValues,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
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
</script>
<?= $this->endSection() ?>