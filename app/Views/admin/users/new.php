<?= $this->extend('layout/template') ?>
<?= $this->section('title') ?>Tambah Pengguna Baru<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Form Tambah Pengguna Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Data Pengguna Baru</h3>
            </div>
            <form action="/admin/users/create" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <?php $errors = session()->get('errors'); ?>
                    <?php if ($errors): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" required>
                        <small class="form-text text-muted">Password akan dikirim ke email pengguna.</small>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirm" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" <?= (old('role') == 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="supervisor" <?= (old('role') == 'supervisor') ? 'selected' : '' ?>>Supervisor</option>
                            <option value="user" <?= (old('role') == 'user') ? 'selected' : '' ?>>User</option>
                            <option value="pemimpin" <?= (old('role') == 'pemimpin') ? 'selected' : '' ?>>Pemimpin</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Simpan Pengguna</button>
                    <a href="/admin/users" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>