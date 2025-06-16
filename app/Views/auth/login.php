<?= $this->extend('layout/auth_template') ?>

<?= $this->section('title') ?>
Halaman Login
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<p class="login-box-msg">Login untuk memulai sesimu</p>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success" role="alert">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger" role="alert">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="/login" method="post">
    <?= csrf_field() ?>
    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="Email" name="email" required>
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
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </div>
    </div>
</form>

<p class="mt-3 mb-1 text-center">
    <a href="/register">Belum punya akun? Daftar</a>
</p>
<?= $this->endSection() ?>