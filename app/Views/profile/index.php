<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Edit Profil
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Edit Profil Saya
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Akun</h3>
            </div>
            <form action="/profile/update" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <?php $errors = session()->get('errors'); ?>
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <p><strong>Input tidak valid:</strong></p>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= esc(session()->get('nama_lengkap')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" value="<?= esc(session()->get('email')) ?>" disabled>
                        <small class="form-text text-muted">Email tidak dapat diubah.</small>
                    </div>
                    <hr>
                    <p class="text-muted">Kosongkan jika tidak ingin mengubah password.</p>
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirm">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>