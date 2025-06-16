<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Tambah Akun Baru
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Form Tambah Akun Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Isi data akun</h3>
            </div>
            <div class="card-body">
                <form action="/coa/create" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="kode_akun">Kode Akun</label>
                        <input type="text" class="form-control" id="kode_akun" name="kode_akun" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_akun">Nama Akun</label>
                        <input type="text" class="form-control" id="nama_akun" name="nama_akun" required>
                    </div>
                    <div class="form-group">
                        <label for="posisi_saldo">Posisi Saldo Normal</label>
                        <select class="form-control" id="posisi_saldo" name="posisi_saldo">
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori_akun">Kategori Akun</label>
                        <select class="form-control" id="kategori_akun" name="kategori_akun">
                            <option value="Aset">Aset</option>
                            <option value="Liabilitas">Liabilitas</option>
                            <option value="Ekuitas">Ekuitas</option>
                            <option value="Pendapatan">Pendapatan</option>
                            <option value="Beban">Beban</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="/coa" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>