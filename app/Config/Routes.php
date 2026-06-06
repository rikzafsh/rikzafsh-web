<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
service('auth')->routes($routes);
// Route untuk halaman dasbor, dikunci dengan filter 'session'
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'session']);
// Mengelompokkan rute artikel agar harus login
// Mengelompokkan rute dashboard agar harus login
$routes->group('dashboard', ['filter' => 'session'], static function ($routes) {
    // 1. Beranda Dashboard
    $routes->get('/', 'Dashboard::index'); 
    
    // 2. Profil (DIUBAH KE SINI)
    $routes->get('profil', 'Dashboard::profil'); 
    $routes->post('profil/update', 'Dashboard::profilUpdate'); // Mengarah ke fungsi profilUpdate
    
    // 3. Kelola Artikel
    $routes->get('artikel', 'Artikel::index');
    // Rute CRUD Controller Artikel
    $routes->get('artikel/tambah', 'Artikel::tambah'); 
    $routes->post('artikel/simpan', 'Artikel::simpan'); 
    
    $routes->get('artikel/edit/(:num)', 'Artikel::edit/$1'); 
    $routes->post('artikel/update/(:num)', 'Artikel::update/$1'); 
    
    $routes->get('artikel/hapus/(:num)', 'Artikel::hapus/$1');

    $routes->get('kategori/tambah', 'Artikel::kategoriTambah');
    $routes->post('kategori/simpan', 'Artikel::kategoriSimpan');
    $routes->get('kategori/edit/(:num)', 'Artikel::kategoriEdit/$1');
    $routes->post('kategori/update/(:num)', 'Artikel::kategoriUpdate/$1');
    $routes->get('kategori/hapus/(:num)', 'Artikel::kategoriHapus/$1');

    $routes->get('users', 'Dashboard::manageUsers');
    $routes->post('users/update-role/(:num)', 'Dashboard::updateUserRole/$1');


});
// Rute Publik untuk melihat daftar artikel
$routes->get('artikel', 'Blog::index');
$routes->get('artikel/(:segment)', 'Blog::read/$1');
$routes->post('artikel/komentar', 'Blog::submitKomentar');
$routes->post('artikel/reaksi', 'Blog::reaksiArtikel');
$routes->post('komentar/reaksi', 'Blog::reaksiKomentar');
$routes->post('artikel/simpan', 'Blog::simpanArtikel');

$routes->get('profile/(:segment)', 'Profil::index/$1');

$routes->get('projects', 'Projects::index');

// Grup utama khusus developer
$routes->group('dashboard/projects', ['filter' => 'group:developer'], function($routes) {
    // 1. Route untuk menampilkan halaman (Tab View)
    $routes->get('', 'Dashboard\ProjectsDashboardController::index');

    // 2. Route khusus aksi Pustaka Karya
    $routes->post('pustaka/simpan', 'Dashboard\PustakaKaryaController::simpan');
    $routes->post('pustaka/update/(:num)', 'Dashboard\PustakaKaryaController::update/$1');
    $routes->get('pustaka/hapus/(:num)', 'Dashboard\PustakaKaryaController::hapus/$1');

    // Nanti untuk Web Apps bisa ditambah di sini:
    // $routes->post('webapps/simpan', 'Dashboard\WebAppsController::simpan');
});



$routes->group('HafidzQuiz', ['filter' => 'roleCheck'], function($routes) {
    $routes->get('/', 'HafidzQuiz::index');
    $routes->get('play', 'HafidzQuiz::play');
    $routes->get('generate', 'HafidzQuiz::generate');
});


// --- ROUTING HAFIDZQUIZ GAME (Halaman yang diakses langsung) ---
$routes->get('hafidzquiz/map', 'HafidzQuizGame::index');
$routes->get('hafidzquiz/play', 'HafidzQuizGame::play');

// --- ROUTING API HAFIDZQUIZ (Akses Data dari JS) ---
// Pastikan route ini berada di dalam proteksi login filter agar aman
$routes->group('HafidzQuizGame', ['filter' => 'roleCheck'], function($routes) {
    $routes->get('getMapData', 'HafidzQuizGame::getMapData');
    $routes->post('submitResult', 'HafidzQuizGame::submitResult');
});

