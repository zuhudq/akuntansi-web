<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Persetujuan Pengguna
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Persetujuan Pengguna
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengguna Menunggu Persetujuan</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendingUsers)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada pengguna yang menunggu persetujuan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1;
                    foreach ($pendingUsers as $user) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($user['nama_lengkap']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td><span class="badge bg-warning"><?= esc($user['role']) ?></span></td>
                            <td><?= date('d M Y, H:i', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="/admin/approve/<?= $user['id'] ?>" class="btn btn-sm btn-success">Setujui</a>
                                <a href="/admin/reject/<?= $user['id'] ?>" class="btn btn-sm btn-danger btn-delete">Tolak</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('page_scripts') ?>
<script>
    $(function() {
        // Script ini akan otomatis bekerja untuk semua tombol dengan class .btn-delete
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Anda akan menolak pendaftaran pengguna ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Warna merah untuk tolak
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tolak Pendaftaran!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>