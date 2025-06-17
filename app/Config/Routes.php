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
// RUTE-RUTE TERPROTEKSI
//====================================================================

// GRUP 1: Rute untuk SEMUA user yang sudah login (Admin, Supervisor, User)
//====================================================================
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->post('profile/delete', 'Profile::deleteAccount');
    // Rute halaman utama setelah login
    $routes->get('/', 'Dashboard::index');

    // --- Rute untuk Modul Jurnal Umum (Semua role bisa akses) ---
    $routes->get('jurnal', 'Jurnal::index');
    $routes->get('jurnal/new', 'Jurnal::new');
    $routes->post('jurnal/create', 'Jurnal::create');
    $routes->get('jurnal/detail/(:num)', 'Jurnal::detail/$1');
    $routes->get('jurnal/edit/(:num)', 'Jurnal::edit/$1');
    $routes->post('jurnal/update/(:num)', 'Jurnal::update/$1');
    $routes->get('jurnal/delete/(:num)', 'Jurnal::delete/$1');

    // --- Rute untuk Profil Pengguna (Semua role bisa akses profilnya sendiri) ---
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
});


// GRUP 2: Rute hanya untuk ADMIN
//====================================================================
// Filter 'auth' memastikan harus login, filter 'role:admin' memastikan rolenya harus admin.
$routes->group('', ['filter' => ['auth', 'role:admin']], function ($routes) {

    // --- Rute untuk Modul Chart of Accounts ---
    $routes->get('coa', 'Coa::index');
    $routes->get('coa/new', 'Coa::new');
    $routes->post('coa/create', 'Coa::create');
    $routes->get('coa/edit/(:num)', 'Coa::edit/$1');
    $routes->post('coa/update/(:num)', 'Coa::update/$1');
    $routes->get('coa/delete/(:num)', 'Coa::delete/$1');

    // --- Rute untuk Admin Area (BARU) ---
    $routes->get('admin/pending-approvals', 'Admin::pendingApprovals');
    $routes->get('admin/approve/(:num)', 'Admin::approve/$1');
    $routes->get('admin/reject/(:num)', 'Admin::reject/$1');
});

// GRUP 3: Rute hanya untuk ADMIN dan SUPERVISOR
//====================================================================
$routes->group('', ['filter' => ['auth', 'role:admin,supervisor']], function ($routes) {

    // --- Rute untuk Modul Laporan ---
    $routes->get('laporan/buku-besar', 'Laporan::bukuBesar');
    $routes->get('laporan/laba-rugi', 'Laporan::labaRugi');
    $routes->get('laporan/neraca', 'Laporan::neraca');
    $routes->get('laporan/cetak-buku-besar', 'Laporan::cetakBukuBesar');
    $routes->get('laporan/cetak-laba-rugi', 'Laporan::cetakLabaRugi');
    $routes->get('laporan/cetak-neraca', 'Laporan::cetakNeraca');
});
