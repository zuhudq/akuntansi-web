<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Input Jurnal Umum
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Form Input Jurnal Umum
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-body">
        <form action="/jurnal/create" method="post">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_jurnal">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="tanggal_jurnal" name="tanggal_jurnal" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="1" required></textarea>
                    </div>
                </div>
            </div>

            <hr>
            <p><strong>Detail Transaksi (Debit & Kredit)</strong></p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Debit</label>
                        <select name="id_akun_debit" class="form-control" required>
                            <option value="">-- Pilih Akun --</option>
                            <?php foreach ($accounts as $account) : ?>
                                <option value="<?= $account['id_akun'] ?>"><?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="debit_amount">Jumlah Debit</label>
                        <input type="number" step="0.01" class="form-control" name="debit" id="debit_amount" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Kredit</label>
                        <select name="id_akun_kredit" class="form-control" required>
                            <option value="">-- Pilih Akun --</option>
                            <?php foreach ($accounts as $account) : ?>
                                <option value="<?= $account['id_akun'] ?>"><?= $account['kode_akun'] ?> - <?= $account['nama_akun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kredit_amount">Jumlah Kredit</label>
                        <input type="number" step="0.01" class="form-control" name="kredit" id="kredit_amount" required>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
            </div>

        </form>
    </div>
</div>
<script>
    // 1. Ambil elemen input debit dan kredit berdasarkan ID
    const debitInput = document.getElementById('debit_amount');
    const kreditInput = document.getElementById('kredit_amount');

    // 2. Tambahkan event listener saat user mengetik di kolom debit
    debitInput.addEventListener('input', function() {
        // Apapun yang diketik di sini, langsung salin nilainya ke kolom kredit
        kreditInput.value = this.value;
    });

    // 3. Lakukan hal yang sama untuk kolom kredit
    kreditInput.addEventListener('input', function() {
        // Apapun yang diketik di sini, langsung salin nilainya ke kolom debit
        debitInput.value = this.value;
    });
</script>

<?= $this->endSection() ?>