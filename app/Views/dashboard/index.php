<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rp <?= number_format($totalAset, 0, ',', '.') ?></h3>
                <p>Total Aset</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="/laporan/neraca" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp <?= number_format($totalLiabilitasEkuitas, 0, ',', '.') ?></h3>
                <p>Total Liabilitas & Ekuitas</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="/laporan/neraca" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $jumlahAkun ?></h3>
                <p>Jumlah Akun (COA)</p>
            </div>
            <div class="icon">
                <i class="ion ion-document-text"></i>
            </div>
            <a href="/coa" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $jumlahJurnal ?></h3>
                <p>Jumlah Transaksi Jurnal</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="/jurnal" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Pendapatan vs Beban Tahun <?= date('Y') ?></h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> <?= $this->section('page_scripts') ?>
<script>
    // Ambil data dari PHP yang sudah kita siapkan di Controller
    var monthlyIncomeData = JSON.parse('<?= $monthlyIncome ?>');
    var monthlyExpenseData = JSON.parse('<?= $monthlyExpense ?>');
    var barChartCanvas = document.getElementById('barChart').getContext('2d');

    var barChartData = {
        labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        datasets: [{
                label: 'Pendapatan',
                backgroundColor: 'rgba(60,141,188,0.9)', // Biru
                borderColor: 'rgba(60,141,188,0.8)',
                data: monthlyIncomeData
            },
            {
                label: 'Beban',
                backgroundColor: 'rgba(220, 53, 69, 0.9)', // Merah
                borderColor: 'rgba(220, 53, 69, 0.8)',
                data: monthlyExpenseData
            },
        ]
    };

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    // Format angka menjadi Rupiah
                    callback: function(value, index, values) {
                        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            }]
        }
    };

    // Buat grafik baru
    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    });
</script>
<?= $this->endSection() ?>