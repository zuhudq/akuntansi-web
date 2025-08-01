<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Projek Akuntansi | <?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/" class="brand-link">
                <img src="<?= base_url('assets/dist/img/AdminLTELogo.png') ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Akuntansi Web</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?= base_url('uploads/avatars/' . (session()->get('avatar') ?? 'default_avatar.png')) ?>" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="/profile" class="d-block"><?= session()->get('nama_lengkap') ?? 'Nama Pengguna' ?></a>
                        <a href="/logout" class="d-block" style="font-size: 0.8em; color: #c2c7d0;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="/" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <?php if (session()->get('role') == 'admin') : ?>
                            <li class="nav-header">MASTER DATA</li>
                            <li class="nav-item">
                                <a href="/coa" class="nav-link">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Chart of Accounts</p>
                                </a>
                            </li>
                            <li class="nav-header">ADMIN AREA</li>
                            <li class="nav-item">
                                <a href="/admin/pending-approvals" class="nav-link">
                                    <i class="nav-icon fas fa-user-check"></i>
                                    <p>Persetujuan Pengguna</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/admin/users" class="nav-link">
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>Manajemen Pengguna</p>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-header">TRANSAKSI</li>
                        <li class="nav-item">
                            <a href="/jurnal" class="nav-link">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>Jurnal Umum</p>
                            </a>
                        </li>
                        <?php if (in_array(session()->get('role'), ['admin', 'supervisor'])) : ?>
                            <li class="nav-header">LAPORAN</li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                    <p>
                                        Laporan Keuangan
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="/laporan/buku-besar" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Buku Besar</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/laporan/laba-rugi" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Laba Rugi</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/laporan/neraca" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Neraca</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $this->renderSection('page_title') ?></h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Projek Akuntansi CI4
            </div>
            <strong>Copyright &copy; 2024-<?= date('Y') ?> <a href="#">Kita</a>.</strong> All rights reserved.
        </footer>
    </div>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/dist/js/adminlte.min.js') ?>"></script>
    <script src="<?= base_url('assets/dist/js/adminlte.min.js') ?>"></script>

    <script src="<?= base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>

    <?= $this->renderSection('page_scripts') ?>
</body>

<?= $this->renderSection('page_scripts') ?>

<script>
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "<?= session()->getFlashdata('success') ?>",
            timer: 2500,
            showConfirmButton: false
        });
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "<?= session()->getFlashdata('error') ?>",
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>
</script>
</body>

</html>