<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Edit Jurnal Umum
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Form Edit Jurnal Umum
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">
        <form action="/jurnal/update/<?= $journalHeader['id_jurnal'] ?>" method="post">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_jurnal">Tanggal Transaksi</label>
                        <input type="date" class="form-control" name="tanggal_jurnal" value="<?= esc($journalHeader['tanggal_jurnal']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="1" required><?= esc($journalHeader['deskripsi']) ?></textarea>
                    </div>
                </div>
            </div>
            <hr>
            <p><strong>Detail Transaksi (Debit & Kredit)</strong></p>

            <?php
            // Logika untuk memisahkan data debit dan kredit dari detail
            // untuk mengisi form dengan benar
            $debitDetail = null;
            $kreditDetail = null;
            foreach ($journalDetails as $detail) {
                if ($detail['debit'] > 0) $debitDetail = $detail;
                if ($detail['kredit'] > 0) $kreditDetail = $detail;
            }
            ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Debit</label>
                        <select name="id_akun_debit" class="form-control" required>
                            <?php foreach ($allAccounts as $account) : ?>
                                <option value="<?= $account['id_akun'] ?>" <?= ($account['id_akun'] == $debitDetail['id_akun']) ? 'selected' : '' ?>>
                                    <?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="debit_amount">Jumlah Debit</label>
                        <input type="number" step="0.01" class="form-control" name="debit" id="debit_amount" value="<?= $debitDetail['debit'] ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Kredit</label>
                        <select name="id_akun_kredit" class="form-control" required>
                            <?php foreach ($allAccounts as $account) : ?>
                                <option value="<?= $account['id_akun'] ?>" <?= ($account['id_akun'] == $kreditDetail['id_akun']) ? 'selected' : '' ?>>
                                    <?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kredit_amount">Jumlah Kredit</label>
                        <input type="number" step="0.01" class="form-control" name="kredit" id="kredit_amount" value="<?= $kreditDetail['kredit'] ?>" required>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="/jurnal" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    const debitInput = document.getElementById('debit_amount');
    const kreditInput = document.getElementById('kredit_amount');
    debitInput.addEventListener('input', function() {
        kreditInput.value = this.value;
    });
    kreditInput.addEventListener('input', function() {
        debitInput.value = this.value;
    });
</script>

<?= $this->endSection() ?>