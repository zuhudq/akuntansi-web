<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Chart of Accounts
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Daftar Akun (Chart of Accounts)
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Seluruh Akun</h3>
        <div class="card-tools">
            <a href="/coa/new" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="data-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th>Kategori</th>
                    <th class="text-right">Saldo Awal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account) : ?>
                    <tr>
                        <td><?= esc($account['kode_akun']) ?></td>
                        <td><?= esc($account['nama_akun']) ?></td>
                        <td><?= esc($account['kategori_akun']) ?></td>
                        <td class="text-right"><?= number_format($account['saldo_awal'], 2, ',', '.') ?></td>
                        <td>
                            <a href="/coa/edit/<?= $account['id_akun'] ?>" class="btn btn-sm btn-info">Edit</a>
                            <a href="/coa/delete/<?= $account['id_akun'] ?>" class="btn btn-sm btn-danger btn-delete">Hapus</a>
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
    // Fungsi ini akan dijalankan setelah seluruh halaman siap
    $(function() {

        // --- Logika untuk Tombol Hapus ---
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            });
        });

        // --- PERBAIKAN: Logika untuk Mengaktifkan DataTables ---
        // Kode ini sekarang diletakkan di sini, BUKAN di dalam fungsi tombol hapus
        $("#data-table").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "ordering": true, // Mengaktifkan sorting
            "info": true, // Menampilkan info halaman
        });

    });
</script>
<?= $this->endSection() ?>