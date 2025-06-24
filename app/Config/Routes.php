<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

//====================================================================
// RUTE PUBLIK (TIDAK BUTUH LOGIN)
//====================================================================
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::processRegister');
$routes->get('/logout', 'Auth::logout');
$routes->get('/verify-email', 'Auth::verifyEmail');


//====================================================================
// RUTE TERPROTEKSI (HARUS LOGIN)
//====================================================================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // --- Rute Halaman Utama & Profil (Semua Role Login) ---
    $routes->get('/', 'Dashboard::index');
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
    $routes->post('profile/delete', 'Profile::deleteAccount');

    // --- Rute Jurnal Umum (KECUALI Pemimpin) ---
    $routes->group('jurnal', ['filter' => 'role:admin,supervisor,user'], function ($routes) {
        $routes->get('', 'Jurnal::index');
        $routes->get('new', 'Jurnal::new');
        $routes->post('create', 'Jurnal::create');
        $routes->get('detail/(:num)', 'Jurnal::detail/$1');
        $routes->get('edit/(:num)', 'Jurnal::edit/$1');
        $routes->post('update/(:num)', 'Jurnal::update/$1');
        $routes->get('delete/(:num)', 'Jurnal::delete/$1');
    });

    // --- Rute COA & Admin Area (Hanya ADMIN) ---
    $routes->group('', ['filter' => 'role:admin'], function ($routes) {
        // Chart of Accounts
        $routes->get('coa', 'Coa::index');
        $routes->get('coa/new', 'Coa::new');
        $routes->post('coa/create', 'Coa::create');
        $routes->get('coa/edit/(:num)', 'Coa::edit/$1');
        $routes->post('coa/update/(:num)', 'Coa::update/$1');
        $routes->get('coa/delete/(:num)', 'Coa::delete/$1');

        // Persetujuan Pengguna
        $routes->get('admin/pending-approvals', 'Admin::pendingApprovals');
        $routes->get('admin/approve/(:num)', 'Admin::approve/$1');
        $routes->get('admin/reject/(:num)', 'Admin::reject/$1');

        // Manajemen Pengguna
        $routes->get('admin/users', 'Admin\Users::index');
        $routes->get('admin/users/new', 'Admin\Users::new');
        $routes->post('admin/users/create', 'Admin\Users::create');
        $routes->get('admin/users/edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('admin/users/update/(:num)', 'Admin\Users::update/$1');
        $routes->get('admin/users/delete/(:num)', 'Admin\Users::delete/$1');
    });

    // --- Rute Laporan (ADMIN, SUPERVISOR, PEMIMPIN) ---
    $routes->group('laporan', ['filter' => 'role:admin,supervisor,pemimpin'], function ($routes) {
        $routes->get('buku-besar', 'Laporan::bukuBesar');
        $routes->get('cetak-buku-besar', 'Laporan::cetakBukuBesar');
        $routes->get('laba-rugi', 'Laporan::labaRugi');
        $routes->get('cetak-laba-rugi', 'Laporan::cetakLabaRugi');
        $routes->get('neraca', 'Laporan::neraca');
        $routes->get('cetak-neraca', 'Laporan::cetakNeraca');
        $routes->get('export-laba-rugi', 'Laporan::exportLabaRugiExcel');
    });

    // --- Rute Dashboard Pimpinan (Hanya PEMIMPIN) ---
    $routes->group('pemimpin', ['filter' => 'role:pemimpin'], function ($routes) {
        $routes->get('dashboard', 'Pemimpin::dashboard');
    });
});
