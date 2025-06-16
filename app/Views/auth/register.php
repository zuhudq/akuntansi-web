<?= $this->extend('layout/auth_template') ?>

<?= $this->section('title') ?>
Halaman Pendaftaran
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<p class="login-box-msg">Daftarkan akun baru</p>

<?php $errors = session()->get('errors'); ?>
<?php if ($errors): ?>
    <div class="alert alert-danger" role="alert">
        <p><strong>Input tidak valid:</strong></p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form action="/register" method="post">
    <?= csrf_field() ?>
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Nama Lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="Email" name="email" value="<?= old('email') ?>" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" class="form-control" placeholder="Password" name="password" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" class="form-control" placeholder="Ulangi Password" name="password_confirm" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <select name="role" class="form-control" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin" <?= (old('role') == 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="supervisor" <?= (old('role') == 'supervisor') ? 'selected' : '' ?>>Supervisor</option>
            <option value="user" <?= (old('role') == 'user') ? 'selected' : '' ?>>User</option>
        </select>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-users-cog"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
        </div>
    </div>
</form>

<p class="mt-3 mb-1 text-center">
    <a href="/login">Saya sudah punya akun</a>
</p>
<?= $this->endSection() ?>