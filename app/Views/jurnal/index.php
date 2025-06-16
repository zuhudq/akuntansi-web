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
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
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
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
<script>
    // Saat dokumen sudah siap
    $(function() {
        // Cari semua tombol dengan class '.btn-delete'
        $('.btn-delete').on('click', function(e) {
            // Hentikan aksi default dari link (agar tidak langsung redirect)
            e.preventDefault();
            // Ambil URL hapus dari atribut href
            const href = $(this).attr('href');

            // Tampilkan popup SweetAlert2
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
                // Jika user menekan tombol "Ya, Hapus Saja!"
                if (result.isConfirmed) {
                    // Arahkan browser ke URL hapus
                    document.location.href = href;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>