<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use App\Models\PustakaKaryaModel;

class Home extends BaseController
{
    public function index(): string
    {
        // Tiga artikel terbaru
        $model = new ArtikelModel();
        $model->kartu()->hanyaTerbit();
        $terbaru = $model->orderBy('artikel.created_at', 'DESC')->findAll(3);

        // Kategori beserta jumlah artikel terbitnya
        $syaratStatus = ArtikelModel::adaKolomStatus() ? " AND artikel.status = 'published'" : '';
        $kategori     = (new KategoriModel())->select(
            'kategori.*, (SELECT COUNT(*) FROM artikel WHERE artikel.kategori_id = kategori.id' . $syaratStatus . ') as total_artikel',
            false
        )->orderBy('nama_kategori', 'ASC')->findAll();

        // Karya (edisi pustaka) terbaru
        $pustakaModel = new PustakaKaryaModel();
        $karya        = $pustakaModel->orderBy('edisi', 'DESC')->findAll(4);
        $totalKarya   = (new PustakaKaryaModel())->countAllResults();

        // Angka ringkas
        $hitung = new ArtikelModel();
        $hitung->hanyaTerbit();
        $totalArtikel = $hitung->countAllResults();

        $jumlah = new ArtikelModel();
        $jumlah->hanyaTerbit();
        $totalViews = (int) ($jumlah->selectSum('artikel.views', 'total')->first()['total'] ?? 0);

        return view('home', [
            'title'         => 'Ruang Rikza | Karya, Tulisan, dan Proyek',
            'fullWidthPage' => true,
            'terbaru'       => $terbaru,
            'kategori'      => $kategori,
            'karya'         => $karya,
            'total_karya'   => $totalKarya,
            'total_artikel' => $totalArtikel,
            'total_views'   => $totalViews,
        ]);
    }
}
