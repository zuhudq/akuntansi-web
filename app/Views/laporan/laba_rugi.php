<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Laporan Laba Rugi
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Laporan Laba Rugi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="/laporan/laba-rugi" method="get">
            <div class="row">
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
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Tampilkan Laporan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($isFiltered) : ?>
    <div class="card">
        <div class="card-header text-center">
            <h4 class="card-title">Laporan Laba Rugi</h4>
            <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></p>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th colspan="2">Pendapatan</th>
                    </tr>
                    <?php foreach ($pendapatanDetails as $item) : ?>
                        <tr>
                            <td style="padding-left: 30px;"><?= esc($item['nama_akun']) ?></td>
                            <td class="text-right">Rp <?= number_format($item['balance'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th style="padding-left: 30px;">Total Pendapatan</th>
                        <th class="text-right">Rp <?= number_format($totalPendapatan, 2, ',', '.') ?></th>
                    </tr>

                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>

                    <tr>
                        <th colspan="2">Beban Operasional</th>
                    </tr>
                    <?php foreach ($bebanDetails as $item) : ?>
                        <tr>
                            <td style="padding-left: 30px;"><?= esc($item['nama_akun']) ?></td>
                            <td class="text-right">Rp <?= number_format($item['balance'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th style="padding-left: 30px;">Total Beban</th>
                        <th class="text-right">Rp <?= number_format($totalBeban, 2, ',', '.') ?></th>
                    </tr>

                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>

                    <tr>
                        <th>LABA / (RUGI) BERSIH</th>
                        <th class="text-right h4">
                            Rp <?= number_format($labaRugi, 2, ',', '.') ?>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>