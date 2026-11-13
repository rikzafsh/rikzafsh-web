<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;

class Artikel extends BaseController
{
    protected $artikelModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->artikelModel  = new ArtikelModel();
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * Admin/developer boleh mengelola semua artikel;
     * user lain hanya artikel miliknya sendiri (kolom penulis_id).
     */
    private function bolehKelola(array $artikel): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->inGroup('admin', 'developer') || (int) $artikel['penulis_id'] === (int) $user->id;
    }

    private function adalahAdmin(): bool
    {
        $user = auth()->user();

        return $user && $user->inGroup('admin', 'developer');
    }

    /**
     * Membuat slug unik. $kecualiId dipakai saat update agar tidak bentrok dengan dirinya sendiri.
     */
    private function buatSlugUnik(string $judul, ?int $kecualiId = null): string
    {
        $dasar = mb_url_title($judul, '-', true);
        if ($dasar === '') {
            $dasar = 'artikel';
        }

        $slug = $dasar;
        $i    = 2;

        while (true) {
            $query = $this->artikelModel->where('slug', $slug);
            if ($kecualiId !== null) {
                $query->where('id !=', $kecualiId);
            }

            if ($query->first() === null) {
                return $slug;
            }

            $slug = $dasar . '-' . $i;
            $i++;
        }
    }

    // Menampilkan daftar artikel (Read)
    public function index()
    {
        $userId = auth()->id();
        $request = \Config\Services::request();

        // -------------------------------------------------------------
        // 1. TAB "ARTIKEL SAYA"
        // -------------------------------------------------------------
        $sortBy    = $request->getGet('sort_by') ?? 'created_at';
        $sortOrder = strtoupper($request->getGet('sort_order') ?? 'DESC');
        if (!in_array($sortOrder, ['ASC', 'DESC'])) $sortOrder = 'DESC';

        // Hitung total artikel milik user
        $totalArtikelSaya = $this->artikelModel->where('penulis_id', $userId)->countAllResults(false);

        $queryArtikelSaya = $this->artikelModel->select('artikel.*, kategori.nama_kategori, kategori.colour,
            (SELECT COUNT(*) FROM komentar WHERE artikel_id = artikel.id) as total_komentar,
            (SELECT COUNT(*) FROM artikel_simpan WHERE artikel_id = artikel.id) as total_simpan', false)
            ->join('kategori', 'kategori.id = artikel.kategori_id', 'left')
            ->where('penulis_id', $userId);

        switch ($sortBy) {
            case 'judul': $queryArtikelSaya->orderBy('artikel.judul', $sortOrder); break;
            case 'kategori': $queryArtikelSaya->orderBy('kategori.nama_kategori', $sortOrder); break;
            case 'views': $queryArtikelSaya->orderBy('artikel.views', $sortOrder); break;
            case 'komentar': $queryArtikelSaya->orderBy('total_komentar', $sortOrder); break;
            case 'simpan': $queryArtikelSaya->orderBy('total_simpan', $sortOrder); break;
            case 'created_at':
            default: $queryArtikelSaya->orderBy('artikel.created_at', $sortOrder); $sortBy = 'created_at'; break;
        }

        $data = [
            'title'              => 'Kelola Artikel | Ruang Rikza',
            'sort_by'            => $sortBy,
            'sort_order'         => $sortOrder,
            'total_artikel_saya' => $totalArtikelSaya,
            'artikel'            => $queryArtikelSaya->paginate(20, 'artikel_saya'), 
            'pager'              => $this->artikelModel->pager
        ];

        // -------------------------------------------------------------
        // 2. TAB "SEMUA ARTIKEL" & "KATEGORI" (Admin/Dev)
        // -------------------------------------------------------------
        if (auth()->user()->inGroup('admin', 'developer')) {
            
            // Hitung total semua artikel
            $data['total_semua_artikel'] = $this->artikelModel->countAllResults(false);

            // Sorting khusus Tab Semua Artikel
            $sortSemua  = $request->getGet('sort_semua') ?? 'created_at';
            $orderSemua = strtoupper($request->getGet('order_semua') ?? 'DESC');
            if (!in_array($orderSemua, ['ASC', 'DESC'])) $orderSemua = 'DESC';

            $querySemua = $this->artikelModel->select('artikel.*, users.username, users.full_name, kategori.nama_kategori, kategori.colour as kat_colour, kategori.slug as kat_slug')
                ->join('users', 'users.id = artikel.penulis_id', 'left')
                ->join('kategori', 'kategori.id = artikel.kategori_id', 'left');

            switch ($sortSemua) {
                case 'judul': $querySemua->orderBy('artikel.judul', $orderSemua); break;
                case 'penulis': $querySemua->orderBy('users.username', $orderSemua); break;
                case 'kategori': $querySemua->orderBy('kategori.nama_kategori', $orderSemua); break;
                case 'views': $querySemua->orderBy('artikel.views', $orderSemua); break;
                case 'created_at':
                default: $querySemua->orderBy('artikel.created_at', $orderSemua); $sortSemua = 'created_at'; break;
            }

            $data['semua_artikel'] = $querySemua->paginate(20, 'semua_artikel');
            $data['sort_semua']    = $sortSemua;
            $data['order_semua']   = $orderSemua;

            // Query Kategori beserta jumlah artikel per kategori
            $data['kategori'] = $this->kategoriModel->select('kategori.*, (SELECT COUNT(id) FROM artikel WHERE kategori_id = kategori.id) as total_artikel')
                ->orderBy('nama_kategori', 'ASC')
                ->paginate(20, 'kategori');
        }

        return view('dashboard/artikel/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title'    => 'Tulis Artikel Baru',
            'kategori' => $this->kategoriModel->findAll(),
        ];

        return view('artikel/create', $data);
    }

    /**
     * Memproses data simpan artikel baru ke database
     */
    public function simpan()
    {
        $rules = [
            'judul'       => 'required|min_length[5]|max_length[200]',
            'kategori_id' => 'required|is_natural_no_zero|is_not_unique[kategori.id]',
            'konten'      => 'required',
            'thumbnail'   => 'max_size[thumbnail,2048]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileThumbnail = $this->request->getFile('thumbnail');
        $namaThumbnail = null;

        if ($fileThumbnail && $fileThumbnail->isValid() && ! $fileThumbnail->hasMoved()) {
            $namaThumbnail = $fileThumbnail->getRandomName();
            $fileThumbnail->move(FCPATH . 'uploads/artikel', $namaThumbnail);
        }

        $judul = trim((string) $this->request->getPost('judul'));

        $this->artikelModel->save([
            'judul'       => $judul,
            'slug'        => $this->buatSlugUnik($judul),
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
            'konten'      => sanitize_html($this->request->getPost('konten')),
            'thumbnail'   => $namaThumbnail,
            'penulis_id'  => auth()->id(),
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Artikel berhasil dipublikasikan!');
    }

    /**
     * Menampilkan form edit artikel
     */
    public function edit($id)
    {
        $artikel = $this->artikelModel->find($id);

        if (! $artikel) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Data artikel tidak ditemukan.');
        }

        if (! $this->bolehKelola($artikel)) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses untuk mengedit artikel ini.');
        }

        $data = [
            'title'    => 'Edit Artikel',
            'artikel'  => $artikel,
            'kategori' => $this->kategoriModel->findAll(),
        ];

        return view('artikel/create', $data);
    }

    /**
     * Memproses update artikel
     */
    public function update($id)
    {
        $artikelLama = $this->artikelModel->find($id);

        if (! $artikelLama) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Data artikel tidak ditemukan.');
        }

        if (! $this->bolehKelola($artikelLama)) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses untuk mengedit artikel ini.');
        }

        $rules = [
            'judul'       => 'required|min_length[5]|max_length[200]',
            'kategori_id' => 'required|is_natural_no_zero|is_not_unique[kategori.id]',
            'konten'      => 'required',
            'thumbnail'   => 'max_size[thumbnail,2048]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileThumbnail = $this->request->getFile('thumbnail');
        $namaThumbnail = $artikelLama['thumbnail'];

        if ($fileThumbnail && $fileThumbnail->isValid() && ! $fileThumbnail->hasMoved()) {
            if ($artikelLama['thumbnail'] && is_file(FCPATH . 'uploads/artikel/' . $artikelLama['thumbnail'])) {
                unlink(FCPATH . 'uploads/artikel/' . $artikelLama['thumbnail']);
            }
            $namaThumbnail = $fileThumbnail->getRandomName();
            $fileThumbnail->move(FCPATH . 'uploads/artikel', $namaThumbnail);
        }

        $judul = trim((string) $this->request->getPost('judul'));

        // Slug hanya berubah jika judul berubah, supaya tautan lama tidak rusak
        $slug = ($judul === $artikelLama['judul'])
            ? $artikelLama['slug']
            : $this->buatSlugUnik($judul, (int) $id);

        $this->artikelModel->update($id, [
            'judul'       => $judul,
            'slug'        => $slug,
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
            'konten'      => sanitize_html($this->request->getPost('konten')),
            'thumbnail'   => $namaThumbnail,
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Perubahan artikel berhasil disimpan!');
    }

    /**
     * Menghapus artikel (hanya lewat POST)
     */
    public function hapus($id)
    {
        $artikel = $this->artikelModel->find($id);

        if (! $artikel) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Artikel gagal dihapus atau tidak ditemukan.');
        }

        if (! $this->bolehKelola($artikel)) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak berhak menghapus artikel ini.');
        }

        if ($artikel['thumbnail'] && is_file(FCPATH . 'uploads/artikel/' . $artikel['thumbnail'])) {
            unlink(FCPATH . 'uploads/artikel/' . $artikel['thumbnail']);
        }

        $this->artikelModel->delete($id);

        return redirect()->to('/dashboard/artikel')->with('message', 'Artikel berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // KATEGORI (khusus admin/developer)
    // ------------------------------------------------------------------

    private function tolakJikaBukanAdmin()
    {
        if (! $this->adalahAdmin()) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses.');
        }

        return null;
    }

    public function kategoriTambah()
    {
        if ($tolak = $this->tolakJikaBukanAdmin()) {
            return $tolak;
        }

        return view('dashboard/kategori/form', [
            'title' => 'Tambah Kategori Baru | Ruang Rikza',
        ]);
    }

    public function kategoriSimpan()
    {
        if ($tolak = $this->tolakJikaBukanAdmin()) {
            return $tolak;
        }

        $rules = [
            'nama_kategori' => 'required|max_length[100]|is_unique[kategori.nama_kategori]',
            'colour'        => 'required|regex_match[/^#[0-9a-fA-F]{6}$/]',
            'cover_image'   => 'max_size[cover_image,2048]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $coverFile = $this->request->getFile('cover_image');
        $coverName = null;
        if ($coverFile && $coverFile->isValid() && ! $coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/kategori', $coverName);
        }

        $this->kategoriModel->save([
            'nama_kategori' => trim((string) $this->request->getPost('nama_kategori')),
            'slug'          => mb_url_title($this->request->getPost('nama_kategori'), '-', true),
            'colour'        => $this->request->getPost('colour'),
            'cover_image'   => $coverName,
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Kategori baru berhasil ditambahkan!');
    }

    public function kategoriEdit($id)
    {
        if ($tolak = $this->tolakJikaBukanAdmin()) {
            return $tolak;
        }

        $kategori = $this->kategoriModel->find($id);
        if (! $kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        return view('dashboard/kategori/form', [
            'title'    => 'Edit Kategori | Ruang Rikza',
            'kategori' => $kategori,
        ]);
    }

    public function kategoriUpdate($id)
    {
        if ($tolak = $this->tolakJikaBukanAdmin()) {
            return $tolak;
        }

        $kategori = $this->kategoriModel->find($id);
        if (! $kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        $id = (int) $id;

        $rules = [
            'nama_kategori' => "required|max_length[100]|is_unique[kategori.nama_kategori,id,{$id}]",
            'colour'        => 'required|regex_match[/^#[0-9a-fA-F]{6}$/]',
            'cover_image'   => 'max_size[cover_image,2048]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $coverFile = $this->request->getFile('cover_image');
        $coverName = $kategori['cover_image'];

        if ($coverFile && $coverFile->isValid() && ! $coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/kategori', $coverName);

            if (! empty($kategori['cover_image']) && is_file(FCPATH . 'uploads/kategori/' . $kategori['cover_image'])) {
                unlink(FCPATH . 'uploads/kategori/' . $kategori['cover_image']);
            }
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => trim((string) $this->request->getPost('nama_kategori')),
            'slug'          => mb_url_title($this->request->getPost('nama_kategori'), '-', true),
            'colour'        => $this->request->getPost('colour'),
            'cover_image'   => $coverName,
            'deskripsi'     => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Kategori berhasil diperbarui!');
    }

    public function kategoriHapus($id)
    {
        if ($tolak = $this->tolakJikaBukanAdmin()) {
            return $tolak;
        }

        $kategori = $this->kategoriModel->find($id);
        if (! $kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        if (! empty($kategori['cover_image']) && is_file(FCPATH . 'uploads/kategori/' . $kategori['cover_image'])) {
            unlink(FCPATH . 'uploads/kategori/' . $kategori['cover_image']);
        }

        $this->kategoriModel->delete($id);

        return redirect()->to('/dashboard/artikel')->with('message', 'Kategori berhasil dihapus!');
    }
}
