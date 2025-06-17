<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Laporan Buku Besar
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Laporan Buku Besar
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="/laporan/buku-besar" method="get">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Pilih Akun</label>
                        <select name="id_akun" class="form-control" required>
                            <option value="">-- Pilih Salah Satu Akun --</option>
                            <?php foreach ($accounts as $account) : ?>
                                <option value="<?= $account['id_akun'] ?>" <?= ($selectedAccountId == $account['id_akun']) ? 'selected' : '' ?>>
                                    <?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
                    </div>
                </div>
                <?php if (!empty($reportData) || (isset($selectedAccount) && $selectedAccount['saldo_awal'] != 0)) : ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <a href="/laporan/cetak-buku-besar?id_akun=<?= $selectedAccountId ?>" class="btn btn-danger btn-block" target="_blank">
                                <i class="fas fa-file-pdf"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if (isset($selectedAccount)) : ?>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                Buku Besar: <?= $selectedAccount['kode_akun'] ?> - <?= $selectedAccount['nama_akun'] ?>
            </h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Kredit</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // PERUBAHAN DI SINI: Saldo tidak lagi dimulai dari 0
                    $saldo = $selectedAccount['posisi_saldo'] == 'debit' ? $selectedAccount['saldo_awal'] : ($selectedAccount['saldo_awal'] * -1);
                    ?>
                    <tr>
                        <td colspan="4"><strong>Saldo Awal</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($saldo, 2, ',', '.') ?></strong></td>
                    </tr>

                    <?php foreach ($reportData as $row) : ?>
                        <?php
                        // Logika perhitungan saldo berjalan sekarang sudah benar karena dimulai dari saldo awal
                        $saldo += $row['debit'] - $row['kredit'];
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tanggal_jurnal'])) ?></td>
                            <td><?= esc($row['deskripsi']) ?></td>
                            <td class="text-right">Rp <?= number_format($row['debit'], 2, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($row['kredit'], 2, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($selectedAccount['posisi_saldo'] == 'debit' ? $saldo : ($saldo * -1), 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>