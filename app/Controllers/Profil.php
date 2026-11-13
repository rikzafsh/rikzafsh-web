<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ArtikelModel; // Sesuaikan dengan nama model artikel Anda!

class Profil extends BaseController
{
    public function index($username)
    {
        $userModel = new UserModel();
        $artikelModel = new ArtikelModel(); // Inisialisasi model artikel
        
        $user = $userModel->where('username', $username)->first();
        
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Pengguna tidak ditemukan.");
        }

        $artikelModel->kartu()->hanyaTerbit();
        $artikelTerbaru = $artikelModel->where('artikel.penulis_id', $user->id)
                                       ->orderBy('artikel.created_at', 'DESC')
                                       ->findAll(6);

        $totalModel = new ArtikelModel();
        $totalModel->hanyaTerbit();
        $totalArtikel = $totalModel->where('artikel.penulis_id', $user->id)->countAllResults();

        $data = [
            'title'          => 'Profil ' . ($user->full_name ?? $user->username) . ' | Ruang Rikza',
            'user'           => $user,
            'artikelTerbaru' => $artikelTerbaru, // Kirim data ke view
            'totalArtikel'   => $totalArtikel,
        ];

        return view('profile/index', $data);
    }
}
