<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Laporan Neraca
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Laporan Neraca
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="/laporan/neraca" method="get">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Posisi per Tanggal</label>
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
            <h4 class="card-title">Laporan Posisi Keuangan (Neraca)</h4>
            <p>Posisi per Tanggal: <?= date('d F Y', strtotime($endDate)) ?></p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ASET</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asetDetails as $item): ?>
                                <tr>
                                    <td><?= esc($item['nama_akun']) ?></td>
                                    <td class="text-right">Rp <?= number_format($item['balance'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>TOTAL ASET</th>
                                <th class="text-right">Rp <?= number_format($totalAset, 2, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>LIABILITAS DAN EKUITAS</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="2">Liabilitas</th>
                            </tr>
                            <?php foreach ($liabilitasDetails as $item): ?>
                                <tr>
                                    <td style="padding-left: 30px;"><?= esc($item['nama_akun']) ?></td>
                                    <td class="text-right">Rp <?= number_format($item['balance'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th style="padding-left: 30px;">Total Liabilitas</th>
                                <th class="text-right border-bottom">Rp <?= number_format($totalLiabilitas, 2, ',', '.') ?></th>
                            </tr>

                            <tr>
                                <th colspan="2">Ekuitas</th>
                            </tr>
                            <?php foreach ($ekuitasDetails as $item): ?>
                                <tr>
                                    <td style="padding-left: 30px;"><?= esc($item['nama_akun']) ?></td>
                                    <td class="text-right">Rp <?= number_format($item['balance'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td style="padding-left: 30px;">Laba (Rugi) Periode Berjalan</td>
                                <td class="text-right">Rp <?= number_format($labaRugiBerjalan, 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th style="padding-left: 30px;">Total Ekuitas</th>
                                <th class="text-right">Rp <?= number_format($totalEkuitas, 2, ',', '.') ?></th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>TOTAL LIABILITAS DAN EKUITAS</th>
                                <th class="text-right">Rp <?= number_format($totalLiabilitas + $totalEkuitas, 2, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>