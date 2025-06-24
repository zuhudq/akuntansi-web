<?= $this->extend('layout/template') ?>
<?= $this->section('title') ?>Edit Pengguna<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Form Edit Pengguna: <?= esc($user['nama_lengkap']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Data Pengguna</h3>
            </div>
            <form action="/admin/users/update/<?= $user['id'] ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <?php if (session()->get('errors')): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session()->get('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= old('nama_lengkap', $user['nama_lengkap']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= old('email', $user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="admin" <?= old('role', $user['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="supervisor" <?= old('role', $user['role']) == 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                            <option value="user" <?= old('role', $user['role']) == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="pemimpin" <?= old('role', $user['role']) == 'pemimpin' ? 'selected' : '' ?>>Pemimpin</option>
                        </select>
                    </div>
                    <hr>
                    <p class="text-muted">Kosongkan jika tidak ingin mengubah password.</p>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirm">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Update Pengguna</button>
                    <a href="/admin/users" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>