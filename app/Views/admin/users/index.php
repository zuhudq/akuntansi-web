<?= $this->extend('layout/template') ?>
<?= $this->section('title') ?>Manajemen Pengguna<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Manajemen Pengguna<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Semua Pengguna</h3>
        <div class="card-tools"><a href="/admin/users/new" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Pengguna</a></div>
    </div>
    <div class="card-body">
        <table id="data-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($user['nama_lengkap']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= esc($user['role']) ?></span></td>
                        <td><span class="badge bg-success"><?= esc($user['status']) ?></span></td>
                        <td>
                            <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                            <a href="/admin/users/delete/<?= $user['id'] ?>" class="btn btn-sm btn-danger btn-delete">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
<script>
    $(function() {
        $("#data-table").DataTable();
        $('.btn-delete').on('click', function(e) {
            /* ... (logika sweetalert) ... */ });
    });
</script>
<?= $this->endSection() ?>