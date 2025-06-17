<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Edit Akun
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Form Edit Akun: <?= esc($account['nama_akun']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit data akun</h3>
            </div>
            <div class="card-body">
                <form action="/coa/update/<?= $account['id_akun'] ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="kode_akun">Kode Akun</label>
                        <input type="text" class="form-control" id="kode_akun" name="kode_akun" value="<?= esc($account['kode_akun']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_akun">Nama Akun</label>
                        <input type="text" class="form-control" id="nama_akun" name="nama_akun" value="<?= esc($account['nama_akun']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="posisi_saldo">Posisi Saldo Normal</label>
                        <select class="form-control" id="posisi_saldo" name="posisi_saldo">
                            <option value="debit" <?= ($account['posisi_saldo'] == 'debit') ? 'selected' : '' ?>>Debit</option>
                            <option value="kredit" <?= ($account['posisi_saldo'] == 'kredit') ? 'selected' : '' ?>>Kredit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori_akun">Kategori Akun</label>
                        <select class="form-control" id="kategori_akun" name="kategori_akun">
                            <option value="Aset" <?= ($account['kategori_akun'] == 'Aset') ? 'selected' : '' ?>>Aset</option>
                            <option value="Liabilitas" <?= ($account['kategori_akun'] == 'Liabilitas') ? 'selected' : '' ?>>Liabilitas</option>
                            <option value="Ekuitas" <?= ($account['kategori_akun'] == 'Ekuitas') ? 'selected' : '' ?>>Ekuitas</option>
                            <option value="Pendapatan" <?= ($account['kategori_akun'] == 'Pendapatan') ? 'selected' : '' ?>>Pendapatan</option>
                            <option value="Beban" <?= ($account['kategori_akun'] == 'Beban') ? 'selected' : '' ?>>Beban</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="saldo_awal">Saldo Awal</label>
                        <input type="number" step="0.01" class="form-control" name="saldo_awal" value="<?= esc($account['saldo_awal']) ?>">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        <a href="/coa" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>