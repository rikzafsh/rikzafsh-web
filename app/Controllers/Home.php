<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;

class Home extends BaseController
{
    public function index(): string
    {
        $model = new ArtikelModel();
        $model->kartu()->hanyaTerbit();
        $terbaru = $model->orderBy('artikel.created_at', 'DESC')->findAll(7);

        $model = new ArtikelModel();
        $model->kartu()->hanyaTerbit();
        $populer = $model->where('artikel.views >', 0)
            ->orderBy('artikel.views', 'DESC')
            ->findAll(4);

        $syaratStatus = ArtikelModel::adaKolomStatus() ? " AND artikel.status = 'published'" : '';
        $kategori     = (new KategoriModel())->select(
            'kategori.*, (SELECT COUNT(*) FROM artikel WHERE artikel.kategori_id = kategori.id' . $syaratStatus . ') as total_artikel',
            false
        )->orderBy('nama_kategori', 'ASC')->findAll();

        // Statistik ringkas untuk bagian hero
        $stat = new ArtikelModel();
        $stat->hanyaTerbit();
        $totalArtikel = $stat->countAllResults();

        return view('home', [
            'title'         => 'Beranda | Ruang Rikza',
            'fullWidthPage' => true,
            'terbaru'       => $terbaru,
            'populer'       => $populer,
            'kategori'      => $kategori,
            'total_artikel' => $totalArtikel,
        ]);
    }
}
