<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use App\Models\KomentarModel;
use App\Models\ArtikelReaksiModel;
use App\Models\KomentarReaksiModel;

class Blog extends BaseController
{
    private const PER_HALAMAN = 9;

    /**
     * Daftar artikel dengan pencarian, filter kategori/penulis, pengurutan, dan pagination.
     */
    public function index()
    {
        $artikelModel  = new ArtikelModel();
        $kategoriModel = new KategoriModel();

        $keyword  = trim((string) $this->request->getGet('cari'));
        $kategori = trim((string) $this->request->getGet('kategori'));
        $penulis  = trim((string) $this->request->getGet('penulis'));
        $sort     = (string) $this->request->getGet('sort');

        if (! in_array($sort, ['terbaru', 'terlama', 'populer'], true)) {
            $sort = 'terbaru';
        }

        $artikelModel->kartu()->hanyaTerbit();

        if ($keyword !== '') {
            $artikelModel->groupStart()
                ->like('artikel.judul', $keyword)
                ->orLike('artikel.konten', $keyword)
                ->groupEnd();
        }

        if ($kategori !== '') {
            $artikelModel->where('kategori.slug', $kategori);
        }

        if ($penulis !== '') {
            $artikelModel->where('users.username', $penulis);
        }

        if ($sort === 'terlama') {
            $artikelModel->orderBy('artikel.created_at', 'ASC');
        } elseif ($sort === 'populer') {
            $artikelModel->orderBy('artikel.views', 'DESC')->orderBy('artikel.created_at', 'DESC');
        } else {
            $artikelModel->orderBy('artikel.created_at', 'DESC');
        }

        $daftarArtikel = $artikelModel->paginate(self::PER_HALAMAN);
        $pager         = $artikelModel->pager;

        // Kategori beserta jumlah artikel terbitnya (untuk chip filter)
        $syaratStatus  = ArtikelModel::adaKolomStatus() ? " AND artikel.status = 'published'" : '';
        $semuaKategori = $kategoriModel->select(
            'kategori.*, (SELECT COUNT(*) FROM artikel WHERE artikel.kategori_id = kategori.id' . $syaratStatus . ') as total_artikel',
            false
        )->orderBy('nama_kategori', 'ASC')->findAll();

        $namaPenulisAktif = '';
        if ($penulis !== '') {
            $user = \Config\Database::connect()->table('users')->where('username', $penulis)->get()->getRow();
            $namaPenulisAktif = $user ? nama_tampil($user->full_name ?? '', $user->username) : '';
        }

        $namaKategoriAktif = '';
        foreach ($semuaKategori as $k) {
            if ($k['slug'] === $kategori) {
                $namaKategoriAktif = $k['nama_kategori'];
            }
        }

        return view('blog/index', [
            'title'              => 'Artikel | Ruang Rikza',
            'fullWidthPage'      => true,
            'daftar_artikel'     => $daftarArtikel,
            'pager'              => $pager,
            'total_hasil'        => $pager->getTotal(),
            'halaman_aktif'      => $pager->getCurrentPage(),
            'semua_kategori'     => $semuaKategori,
            'keyword'            => $keyword,
            'kategori_aktif'     => $kategori,
            'nama_kategori_aktif' => $namaKategoriAktif,
            'penulis_aktif'      => $penulis,
            'nama_penulis_aktif' => $namaPenulisAktif,
            'sort_aktif'         => $sort,
        ]);
    }

    /**
     * Halaman baca artikel: konten, reaksi, komentar, dan artikel terkait.
     */
    public function read($slug)
    {
        $artikelModel  = new ArtikelModel();
        $komentarModel = new KomentarModel();

        $currentUserId = auth()->loggedIn() ? (int) auth()->id() : 'NULL';

        $cekArtikel = $artikelModel->where('slug', $slug)->first();
        if (! $cekArtikel) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Draf hanya boleh dilihat penulisnya dan admin/developer
        $terbit = ! ArtikelModel::adaKolomStatus() || ($cekArtikel['status'] ?? 'published') === 'published';
        if (! $terbit) {
            $boleh = auth()->loggedIn()
                && (auth()->user()->inGroup('admin', 'developer') || (int) $cekArtikel['penulis_id'] === (int) auth()->id());

            if (! $boleh) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        // Tambah views hanya sekali per sesi (dan hanya untuk artikel terbit)
        $kunciView = 'viewed_artikel_' . $cekArtikel['id'];
        if ($terbit && ! session()->get($kunciView)) {
            $artikelModel->where('id', $cekArtikel['id'])->set('views', 'views + 1', false)->update();
            session()->set($kunciView, true);
        }

        $artikel = $artikelModel->select('artikel.*, users.username, users.full_name, users.avatar, users.bio,
            kategori.nama_kategori, kategori.slug as kat_slug, kategori.colour as kat_colour, kategori.cover_image,
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
        $balasan       = [];
        foreach ($semuaKomentar as $k) {
            if ($k['parent_id'] == null) {
                $komentarUtama[] = $k;
            } else {
                $balasan[$k['parent_id']][] = $k;
            }
        }

        return view('blog/read', [
            'title'          => $artikel['judul'] . ' | Ruang Rikza',
            'art'            => $artikel,
            'komentar'       => $komentarUtama,
            'balasan'        => $balasan,
            'total_komentar' => count($semuaKomentar),
            'terkait'        => $this->artikelTerkait($artikel),
            'terbit'         => $terbit,
            'metaDescription' => ringkasan($artikel['konten'], 155),
            'ogImage'         => ! empty($artikel['thumbnail']) ? base_url('uploads/artikel/' . $artikel['thumbnail']) : null,
            'ogType'          => 'article',
        ]);
    }

    /**
     * Sampai 3 artikel terkait: utamakan kategori yang sama, sisanya diisi artikel terbaru.
     */
    private function artikelTerkait(array $artikel): array
    {
        $hasil = [];

        if (! empty($artikel['kategori_id'])) {
            $model = new ArtikelModel();
            $model->kartu()->hanyaTerbit();
            $hasil = $model->where('artikel.id !=', $artikel['id'])
                ->where('artikel.kategori_id', $artikel['kategori_id'])
                ->orderBy('artikel.created_at', 'DESC')
                ->findAll(3);
        }

        if (count($hasil) < 3) {
            $sudah   = array_merge([(int) $artikel['id']], array_map('intval', array_column($hasil, 'id')));
            $model   = new ArtikelModel();
            $model->kartu()->hanyaTerbit();
            $tambahan = $model->whereNotIn('artikel.id', $sudah)
                ->orderBy('artikel.created_at', 'DESC')
                ->findAll(3 - count($hasil));

            $hasil = array_merge($hasil, $tambahan);
        }

        return $hasil;
    }

    /**
     * Memastikan artikel ada. Mengembalikan id artikel (int) atau null.
     */
    private function artikelValid($id): ?int
    {
        $id = (int) $id;

        if ($id <= 0 || (new ArtikelModel())->find($id) === null) {
            return null;
        }

        return $id;
    }

    /**
     * Alamat halaman artikel (opsional dengan jangkar), dipakai agar pengguna
     * kembali ke bagian halaman yang sedang ia gunakan setelah aksi.
     */
    private function urlArtikel(int $artikelId, string $jangkar = ''): string
    {
        $artikel = (new ArtikelModel())->find($artikelId);

        if (! $artikel) {
            return site_url('artikel');
        }

        return site_url('artikel/' . $artikel['slug']) . ($jangkar !== '' ? '#' . $jangkar : '');
    }

    // FUNGSI LOGIKA TOGGLE SIMPAN ARTIKEL (BOOKMARK)
    public function simpanArtikel()
    {
        if (! auth()->loggedIn()) {
            return redirect()->back()->with('error', 'Masuk dulu untuk menyimpan artikel.');
        }

        $artikelId = $this->artikelValid($this->request->getPost('artikel_id'));
        if ($artikelId === null) {
            return redirect()->back()->with('error', 'Artikel tidak ditemukan.');
        }

        $simpanModel = new \App\Models\ArtikelSimpanModel();
        $userId      = auth()->id();

        $cek = $simpanModel->where('artikel_id', $artikelId)->where('user_id', $userId)->first();

        if ($cek) {
            $simpanModel->delete($cek['id']);
            $pesan = 'Artikel dihapus dari simpanan.';
        } else {
            $simpanModel->save([
                'artikel_id' => $artikelId,
                'user_id'    => $userId,
            ]);
            $pesan = 'Artikel disimpan.';
        }

        return redirect()->to($this->urlArtikel($artikelId, 'reaksi'))->with('message', $pesan);
    }

    public function reaksiArtikel()
    {
        if (! auth()->loggedIn()) {
            return redirect()->back()->with('error', 'Masuk dulu untuk memberi reaksi.');
        }

        $artikelId = $this->artikelValid($this->request->getPost('artikel_id'));
        $jenis     = $this->request->getPost('jenis');

        if ($artikelId === null || ! in_array($jenis, ['like', 'dislike'], true)) {
            return redirect()->back()->with('error', 'Permintaan tidak valid.');
        }

        $reaksiModel = new ArtikelReaksiModel();
        $userId      = auth()->id();

        $cek = $reaksiModel->where('artikel_id', $artikelId)->where('user_id', $userId)->first();

        if ($cek) {
            if ($cek['jenis'] === $jenis) {
                $reaksiModel->delete($cek['id']);
            } else {
                $reaksiModel->update($cek['id'], ['jenis' => $jenis]);
            }
        } else {
            $reaksiModel->save([
                'artikel_id' => $artikelId,
                'user_id'    => $userId,
                'jenis'      => $jenis,
            ]);
        }

        return redirect()->to($this->urlArtikel($artikelId, 'reaksi'));
    }

    public function reaksiKomentar()
    {
        if (! auth()->loggedIn()) {
            return redirect()->back()->with('error', 'Masuk dulu untuk memberi reaksi.');
        }

        $komentarId = (int) $this->request->getPost('komentar_id');
        $jenis      = $this->request->getPost('jenis');
        $komentar   = $komentarId > 0 ? (new KomentarModel())->find($komentarId) : null;

        if ($komentar === null || ! in_array($jenis, ['like', 'dislike'], true)) {
            return redirect()->back()->with('error', 'Permintaan tidak valid.');
        }

        $reaksiModel = new KomentarReaksiModel();
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
                'jenis'       => $jenis,
            ]);
        }

        return redirect()->to($this->urlArtikel((int) $komentar['artikel_id'], 'komentar-' . $komentarId));
    }

    public function submitKomentar()
    {
        if (! auth()->loggedIn()) {
            return redirect()->back()->with('error', 'Masuk dulu untuk berkomentar.');
        }

        $rules = [
            'artikel_id'   => 'required|is_natural_no_zero|is_not_unique[artikel.id]',
            'isi_komentar' => 'required|min_length[2]|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        // Batasi: maksimal 1 komentar tiap 10 detik per pengguna
        $throttler = service('throttler');
        if ($throttler->check('komentar_' . auth()->id(), 1, 10) === false) {
            return redirect()->back()->withInput()->with('error', 'Terlalu cepat, tunggu beberapa detik sebelum berkomentar lagi.');
        }

        $komentarModel = new KomentarModel();
        $artikelId     = (int) $this->request->getPost('artikel_id');
        $parentId      = (int) $this->request->getPost('parent_id');

        // Balasan harus menempel pada komentar utama di artikel yang sama
        if ($parentId > 0) {
            $induk = $komentarModel->find($parentId);

            if ($induk === null || (int) $induk['artikel_id'] !== $artikelId) {
                return redirect()->back()->with('error', 'Komentar yang dibalas tidak valid.');
            }

            // Balasan dari balasan dinaikkan ke komentar utamanya
            if (! empty($induk['parent_id'])) {
                $parentId = (int) $induk['parent_id'];
            }
        }

        $komentarModel->save([
            'artikel_id'   => $artikelId,
            'user_id'      => auth()->id(),
            'parent_id'    => $parentId > 0 ? $parentId : null,
            'isi_komentar' => trim((string) $this->request->getPost('isi_komentar')),
        ]);

        return redirect()->to($this->urlArtikel($artikelId, 'komentar-' . $komentarModel->getInsertID()))
            ->with('message', 'Komentar terkirim.');
    }

    /**
     * Hapus komentar (beserta balasannya). Hanya pemilik komentar atau admin/developer.
     */
    public function hapusKomentar($id)
    {
        if (! auth()->loggedIn()) {
            return redirect()->back()->with('error', 'Masuk dulu.');
        }

        $komentarModel = new KomentarModel();
        $komentar      = $komentarModel->find((int) $id);

        if (! $komentar) {
            return redirect()->back()->with('error', 'Komentar tidak ditemukan.');
        }

        $user = auth()->user();
        if (! ($user->inGroup('admin', 'developer') || (int) $komentar['user_id'] === (int) $user->id)) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus komentar ini.');
        }

        // Hapus balasan dulu (tabel komentar tidak punya foreign key untuk parent_id)
        $komentarModel->where('parent_id', $komentar['id'])->delete();
        $komentarModel->delete($komentar['id']);

        return redirect()->to($this->urlArtikel((int) $komentar['artikel_id'], 'diskusi'))
            ->with('message', 'Komentar dihapus.');
    }
}
