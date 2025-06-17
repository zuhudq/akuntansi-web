<?= $this->extend('layout/template') ?>

<?= $this->section('title') ?>
Daftar Jurnal Umum
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Daftar Jurnal Umum
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Semua Transaksi</h3>
        <div class="card-tools">
            <a href="/jurnal/new" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Jurnal Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="data-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($journals)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data jurnal.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1;
                    foreach ($journals as $journal) : ?>
                        <tr>
                            <td><?= $no++ ?>.</td>
                            <td><?= date('d M Y', strtotime($journal['tanggal_jurnal'])) ?></td>
                            <td><?= esc($journal['deskripsi']) ?></td>
                            <td>
                                <a href="/jurnal/detail/<?= $journal['id_jurnal'] ?>" class="btn btn-sm btn-success">Detail</a>
                                <a href="/jurnal/edit/<?= $journal['id_jurnal'] ?>" class="btn btn-sm btn-info">Edit</a>
                                <a href="/jurnal/delete/<?= $journal['id_jurnal'] ?>" class="btn btn-sm btn-danger btn-delete">Hapus</a>
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
        // --- Logika untuk Tombol Hapus (TETAP SAMA) ---
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Jurnal yang dihapus akan mempengaruhi laporan keuangan!",
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

        // --- PERUBAHAN 2: TAMBAHKAN KODE INI ---
        $("#data-table").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "ordering": true,
            "info": true,
            "order": [
                [0, "desc"]
            ] // Mengurutkan berdasarkan kolom pertama (No.) secara descending
        });
    });
</script>
<?= $this->endSection() ?>