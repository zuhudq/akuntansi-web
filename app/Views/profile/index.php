<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Edit Profil
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Edit Profil Saya
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Akun</h3>
            </div>
            <form action="/profile/update" method="post" enctype="multipart/form-data">
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

                    <div class="form-group text-center">
                        <img src="<?= base_url('uploads/avatars/' . (session()->get('avatar') ?? 'default_avatar.png')) ?>" alt="Avatar Pengguna" class="img-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>

                    <div class="form-group">
                        <label for="avatar">Ganti Foto Profil (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="avatar" name="avatar">
                            <label class="custom-file-label" for="avatar">Pilih gambar... (Max: 1MB)</label>
                        </div>
                    </div>
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

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Danger Zone</h3>
            </div>
            <div class="card-body">
                <p>Menghapus akun Anda bersifat permanen dan tidak dapat dibatalkan. Semua data yang terhubung dengan akun ini akan hilang.</p>
                <form action="/profile/delete" method="post" id="delete-account-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Hapus Akun Saya Secara Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('page_scripts') ?>
<script>
    // Script untuk konfirmasi hapus akun (TETAP SAMA)
    $('#delete-account-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'ANDA SANGAT YAKIN?',
            text: "Tindakan ini benar-benar tidak bisa dibatalkan!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Saya Mengerti dan Ingin Hapus!',
            cancelButtonText: 'Batal, Jangan Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // ======================================================= -->
    //     SCRIPT BARU UNTUK MENAMPILKAN NAMA FILE DI INPUT      -->
    // ======================================================= -->
    $('#avatar').on('change', function() {
        // Ambil nama file
        var fileName = $(this).val().split('\\').pop();
        // Ganti teks label dengan nama file
        $(this).next('.custom-file-label').html(fileName);
    })
</script>
<?= $this->endSection() ?>