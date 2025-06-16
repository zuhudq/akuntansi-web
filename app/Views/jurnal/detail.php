<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Detail Jurnal
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Detail Transaksi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Jurnal Umum</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Tanggal:</strong>
                <p><?= date('d F Y', strtotime($journalHeader['tanggal_jurnal'])) ?></p>
            </div>
            <div class="col-md-6">
                <strong>Deskripsi:</strong>
                <p><?= esc($journalHeader['deskripsi']) ?></p>
            </div>
        </div>

        <hr>

        <h5>Rincian Jurnal:</h5>
        <table class="table table-bordered mt-3">
            <thead class="thead-light">
                <tr>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Kredit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalDebit = 0;
                $totalKredit = 0;
                foreach ($journalDetails as $detail) :
                    $totalDebit += $detail['debit'];
                    $totalKredit += $detail['kredit'];
                ?>
                    <tr>
                        <td><?= esc($detail['kode_akun']) ?></td>
                        <td><?= esc($detail['nama_akun']) ?></td>
                        <td class="text-right">Rp <?= number_format($detail['debit'], 2, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($detail['kredit'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-center">Total</th>
                    <th class="text-right">Rp <?= number_format($totalDebit, 2, ',', '.') ?></th>
                    <th class="text-right">Rp <?= number_format($totalKredit, 2, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="mt-4">
            <a href="/jurnal" class="btn btn-secondary">Kembali ke Daftar Jurnal</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>