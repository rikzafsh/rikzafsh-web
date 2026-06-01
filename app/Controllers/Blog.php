<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use App\Models\KomentarModel;
use App\Models\ArtikelReaksiModel;
use App\Models\KomentarReaksiModel;

class Blog extends BaseController
{
    public function index()
    {
        $artikelModel = new ArtikelModel();
        $kategoriModel = new KategoriModel();

        // Ambil input dari URL query string
        $keyword  = $this->request->getGet('cari');
        $kategori = $this->request->getGet('kategori'); // Sekarang menerima SLUG kategori
        $penulis  = $this->request->getGet('penulis');  // Sekarang menerima USERNAME penulis
        $sort     = $this->request->getGet('sort') ?? 'terbaru';

        // PERBAIKAN: Menambahkan kategori.cover_image pada select dan mengubah join menjadi 'left'
        $query = $artikelModel->select('artikel.*, users.username, users.full_name, kategori.nama_kategori, kategori.slug as kat_slug, kategori.colour as kat_colour, kategori.cover_image')
                              ->join('users', 'users.id = artikel.penulis_id', 'left')
                              ->join('kategori', 'kategori.id = artikel.kategori_id', 'left');

        // 1. FITUR PENCARIAN
        if (!empty($keyword)) {
            $query->groupStart()
                  ->like('artikel.judul', $keyword)
                  ->orLike('artikel.konten', $keyword)
                  ->groupEnd();
        }

        // 2. FITUR FILTER KATEGORI (Berdasarkan Slug Kategori)
        if (!empty($kategori)) {
            $query->where('kategori.slug', $kategori);
        }

        // 3. FITUR FILTER PENULIS (Berdasarkan Username, Lebih Aman!)
        if (!empty($penulis)) {
            $query->where('users.username', $penulis);
        }

        // 4. FITUR SORTING
        if ($sort === 'terlama') {
            $query->orderBy('artikel.created_at', 'ASC');
        } else {
            $query->orderBy('artikel.created_at', 'DESC');
        }

        // Ambil semua daftar kategori untuk isi dropdown filter di view
        $semuaKategori = $kategoriModel->findAll();

        // Cari tahu info nama penulis yang sedang difilter (jika ada) untuk teks indikator
        $namaPenulisAktif = '';
        if (!empty($penulis)) {
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('username', $penulis)->get()->getRow();
            $namaPenulisAktif = $user ? ($user->full_name ?? $user->username) : '';
        }

        $data = [
            'title'              => 'Daftar Artikel | Ruang Rikza',
            'daftar_artikel'     => $query->findAll(),
            'semua_kategori'     => $semuaKategori,
            'keyword'            => $keyword,
            'kategori_aktif'     => $kategori,
            'penulis_aktif'      => $penulis,
            'nama_penulis_aktif' => $namaPenulisAktif,
            'sort_aktif'         => $sort
        ];

        return view('blog/index', $data);
    }

    public function read($slug)
    {
        try {
            $artikelModel = new ArtikelModel();
            $komentarModel = new KomentarModel();
            
            $currentUserId = auth()->loggedIn() ? auth()->id() : "NULL";

            $cekArtikel = $artikelModel->where('slug', $slug)->first();
            if (!$cekArtikel) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

            // Tambah views
            $artikelModel->where('id', $cekArtikel['id'])->set('views', 'views + 1', false)->update();

            // PERBAIKAN: Menambahkan kategori.cover_image pada select dan mengubah join menjadi 'left'
            $artikel = $artikelModel->select('artikel.*, users.username, users.full_name, users.avatar, kategori.nama_kategori, kategori.colour as kat_colour, kategori.cover_image,
                (SELECT COUNT(*) FROM artikel_reaksi WHERE artikel_id = artikel.id AND jenis = \'like\') as total_like,
                (SELECT COUNT(*) FROM artikel_reaksi WHERE artikel_id = artikel.id AND jenis = \'dislike\') as total_dislike,
                (SELECT jenis FROM artikel_reaksi WHERE artikel_id = artikel.id AND user_id = ' . $currentUserId . ') as reaksi_user,
                (SELECT COUNT(*) FROM artikel_simpan WHERE artikel_id = artikel.id) as total_simpan,
                (SELECT COUNT(*) FROM artikel_simpan WHERE artikel_id = artikel.id AND user_id = ' . $currentUserId . ') as is_saved', false) 
                                    ->join('users', 'users.id = artikel.penulis_id', 'left')
                                    ->join('kategori', 'kategori.id = artikel.kategori_id', 'left')
                                    ->where('artikel.id', $cekArtikel['id'])
                                    ->first();

            $semuaKomentar = $komentarModel->getKomentarByArtikel($artikel['id'], $currentUserId);

            $komentarUtama = [];
            $balasan = [];
            foreach ($semuaKomentar as $k) {
                if ($k['parent_id'] == null) {
                    $komentarUtama[] = $k;
                } else {
                    $balasan[$k['parent_id']][] = $k;
                }
            }

            $data = [
                'title'    => $artikel['judul'] . ' | Ruang Rikza',
                'art'      => $artikel,
                'komentar' => $komentarUtama,
                'balasan'  => $balasan
            ];

            return view('blog/read', $data);

        } catch (\Exception $e) {
            // Trik paksa cetak error!
            die("<div style='padding:30px; background:#ffebeb; border-left: 5px solid red; color:#333; font-family:sans-serif;'>
                    <h2 style='color:red;'>🚨 Ditemukan Error:</h2>
                    <p style='font-size:1.2em;'>" . $e->getMessage() . "</p>
                 </div>");
        }
    }

    // 3. FUNGSI LOGIKA TOGGLE SIMPAN ARTIKEL (BOOKMARK)
    public function simpanArtikel()
    {
        if (!auth()->loggedIn()) return redirect()->back()->with('error', 'Login dulu yuk!');

        $simpanModel = new \App\Models\ArtikelSimpanModel();
        $artikelId   = $this->request->getPost('artikel_id');
        $userId      = auth()->id();

        // Cek jika sudah pernah disimpan, maka hapus (unsave). Jika belum, maka simpan.
        $cek = $simpanModel->where('artikel_id', $artikelId)->where('user_id', $userId)->first();

        if ($cek) {
            $simpanModel->delete($cek['id']);
        } else {
            $simpanModel->save([
                'artikel_id' => $artikelId,
                'user_id'    => $userId
            ]);
        }
        return redirect()->back();
    }

    public function reaksiArtikel()
    {
        if (!auth()->loggedIn()) return redirect()->back()->with('error', 'Login dulu yuk!');

        $reaksiModel = new ArtikelReaksiModel();
        $artikelId   = $this->request->getPost('artikel_id');
        $jenis       = $this->request->getPost('jenis'); // 'like' atau 'dislike'
        $userId      = auth()->id();

        // Cek apakah user sudah pernah bereaksi di artikel ini
        $cek = $reaksiModel->where('artikel_id', $artikelId)->where('user_id', $userId)->first();

        if ($cek) {
            if ($cek['jenis'] === $jenis) {
                // Jika klik tombol yang sama, batalkan reaksi (hapus)
                $reaksiModel->delete($cek['id']);
            } else {
                // Jika klik tombol berbeda, ganti jenis reaksinya
                $reaksiModel->update($cek['id'], ['jenis' => $jenis]);
            }
        } else {
            // Jika belum pernah, buat reaksi baru
            $reaksiModel->save([
                'artikel_id' => $artikelId,
                'user_id'    => $userId,
                'jenis'      => $jenis
            ]);
        }
        return redirect()->back();
    }

    public function reaksiKomentar()
    {
        if (!auth()->loggedIn()) return redirect()->back()->with('error', 'Login dulu yuk!');

        $reaksiModel = new KomentarReaksiModel();
        $komentarId  = $this->request->getPost('komentar_id');
        $jenis       = $this->request->getPost('jenis');
        $userId      = auth()->id();

        $cek = $reaksiModel->where('komentar_id', $komentarId)->where('user_id', $userId)->first();

        if ($cek) {
            if ($cek['jenis'] === $jenis) {
                $reaksiModel->delete($cek['id']);
            } else {
                $reaksiModel->update($cek['id'], ['jenis' => $jenis]);
            }
        } else {
            $reaksiModel->save([
                'komentar_id' => $komentarId,
                'user_id'     => $userId,
                'jenis'       => $jenis
            ]);
        }
        return redirect()->back();
    }

    public function submitKomentar()
    {
        if (!auth()->loggedIn()) return redirect()->back()->with('error', 'Login dulu ya!');
        
        $komentarModel = new KomentarModel();
        $komentarModel->save([
            'artikel_id'   => $this->request->getPost('artikel_id'),
            'user_id'      => auth()->id(),
            'parent_id'    => $this->request->getPost('parent_id') ?: null,
            'isi_komentar' => $this->request->getPost('isi_komentar'),
        ]);

        return redirect()->back()->with('pesan', 'Komentar terkirim!');
    }
}