<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel; // Jangan lupa tambahkan ini!

class Artikel extends BaseController
{
    protected $artikelModel;
    protected $kategoriModel; // Tambahkan properti untuk model kategori

    public function __construct()
    {
        $this->artikelModel  = new ArtikelModel();
        $this->kategoriModel = new KategoriModel(); // Inisialisasi model kategori
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

    // Menampilkan form tambah artikel (Create)
    public function create()
    {
        return view('artikel/create');
    }

    // Menyimpan data ke database & upload gambar (Store)
    public function store()
    {
        // 1. Ambil file gambar
        $fileGambar = $this->request->getFile('thumbnail');
        $namaGambar = null;

        // 2. Cek apakah ada gambar yang diupload
        if ($fileGambar && $fileGambar->isValid() && ! $fileGambar->hasMoved()) {
            $namaGambar = $fileGambar->getRandomName(); // Bikin nama file acak
            $fileGambar->move('uploads/artikel', $namaGambar); // Pindahkan ke folder public/uploads/artikel
        }

        // 3. Bikin Slug (URL ramah) dari Judul (contoh: "Halo Budi" jadi "halo-budi")
        $slug = url_title($this->request->getPost('judul'), '-', true);

        // 4. Simpan ke database
        $this->artikelModel->save([
            'judul'      => $this->request->getPost('judul'),
            'slug'       => $slug,
            'konten'     => $this->request->getPost('konten'),
            'kategori'   => $this->request->getPost('kategori'),
            'thumbnail'  => $namaGambar,
            'penulis_id' => auth()->id(), // Ambil ID user yang sedang login
        ]);

        return redirect()->to('/dashboard/artikel')->with('pesan', 'Artikel berhasil ditambahkan.');
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tulis Artikel Baru',
            'kategori' => $this->kategoriModel->findAll()
        ];

        return view('artikel/create', $data); 
    }

    /**
     * Memproses Data Simpan Artikel Baru ke Database
     */
    public function simpan()
    {
        $rules = [
            'judul'       => 'required|min_length[5]',
            'kategori_id' => 'required',
            'konten'      => 'required',
            'thumbnail'   => 'max_size[thumbnail,2048]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileThumbnail = $this->request->getFile('thumbnail');
        $namaThumbnail = null;

        if ($fileThumbnail && $fileThumbnail->isValid() && !$fileThumbnail->hasMoved()) {
            $namaThumbnail = $fileThumbnail->getRandomName();
            $fileThumbnail->move('uploads/artikel', $namaThumbnail);
        }

        $slug = url_title($this->request->getPost('judul'), '-', true);

        $this->artikelModel->save([
            'judul'       => $this->request->getPost('judul'),
            'slug'        => $slug,
            'kategori_id' => $this->request->getPost('kategori_id'),
            'konten'      => $this->request->getPost('konten'),
            'thumbnail'   => $namaThumbnail,
            'penulis_id'     => auth()->id(), 
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Artikel berhasil dipublikasikan!');
    }

    /**
     * Menampilkan Form Edit Artikel
     */
    public function edit($id)
    {
        $artikel = $this->artikelModel->find($id);

        if (!$artikel) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Data artikel tidak ditemukan.');
        }

        // Cek apakah user ada sebelum mengecek grup
        $user = auth()->user();
        $isAdminOrDev = $user ? $user->inGroup('admin', 'developer') : false;
        
        // PERBAIKAN: Gunakan "??" agar tidak error jika kolom user_id tidak ada di database
        // Jika nama kolom di database Anda berbeda (misal: 'id_user'), ubah kata 'user_id' di bawah ini
        $idPenulis = $artikel['user_id'] ?? null;
        
        if ($idPenulis !== null && $idPenulis != auth()->id() && !$isAdminOrDev) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses untuk mengedit artikel ini.');
        }

        $data = [
            'title'    => 'Edit Artikel',
            'artikel'  => $artikel,
            'kategori' => $this->kategoriModel->findAll()
        ];

        return view('artikel/create', $data);
    }

    /**
     * Memproses Data Update Artikel ke Database
     */
    public function update($id)
    {
        $artikelLama = $this->artikelModel->find($id);
        
        $rules = [
            'judul'       => 'required|min_length[5]',
            'kategori_id' => 'required',
            'konten'      => 'required',
            'thumbnail'   => 'max_size[thumbnail,2048]|is_image[thumbnail]|mime_in[thumbnail,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fileThumbnail = $this->request->getFile('thumbnail');
        $namaThumbnail = $artikelLama['thumbnail']; 

        if ($fileThumbnail && $fileThumbnail->isValid() && !$fileThumbnail->hasMoved()) {
            if ($artikelLama['thumbnail'] && file_exists('uploads/artikel/' . $artikelLama['thumbnail'])) {
                unlink('uploads/artikel/' . $artikelLama['thumbnail']);
            }
            $namaThumbnail = $fileThumbnail->getRandomName();
            $fileThumbnail->move('uploads/artikel', $namaThumbnail);
        }

        $slug = url_title($this->request->getPost('judul'), '-', true);

        $this->artikelModel->update($id, [
            'judul'       => $this->request->getPost('judul'),
            'slug'        => $slug,
            'kategori_id' => $this->request->getPost('kategori_id'),
            'konten'      => $this->request->getPost('konten'),
            'thumbnail'   => $namaThumbnail
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Perubahan artikel berhasil disimpan!');
    }

    /**
     * Menghapus Artikel
     */
    public function hapus($id)
    {
        $artikel = $this->artikelModel->find($id);

        if ($artikel) {
            $user = auth()->user();
            $isAdminOrDev = $user ? $user->inGroup('admin', 'developer') : false;

            // PERBAIKAN SAMA: Gunakan "??"
            $idPenulis = $artikel['user_id'] ?? null;

            if ($idPenulis !== null && $idPenulis != auth()->id() && !$isAdminOrDev) {
                return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak berhak menghapus artikel ini.');
            }

            if ($artikel['thumbnail'] && file_exists('uploads/artikel/' . $artikel['thumbnail'])) {
                unlink('uploads/artikel/' . $artikel['thumbnail']);
            }

            $this->artikelModel->delete($id);
            return redirect()->to('/dashboard/artikel')->with('message', 'Artikel berhasil dihapus.');
        }

        return redirect()->to('/dashboard/artikel')->with('error', 'Artikel gagal dihapus atau tidak ditemukan.');
    }
    
    // Fitur Delete
    public function delete($id)
    {
        $this->artikelModel->delete($id);
        return redirect()->to('/dashboard/artikel')->with('pesan', 'Artikel berhasil dihapus.');
    }

    // 1. Tampilan Form Tambah Kategori
    public function kategoriTambah()
    {
        if (!auth()->user()->inGroup('admin', 'developer')) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses.');
        }

        $data = [
            'title' => 'Tambah Kategori Baru | Ruang Rikza',
        ];

        return view('dashboard/kategori/form', $data);
    }

    // 2. Proses Simpan Kategori Baru ke Database
    public function kategoriSimpan()
    {
        if (!auth()->user()->inGroup('admin', 'developer')) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses.');
        }

        // Aturan validasi input teks dan file gambar
        $rules = [
            'nama_kategori' => 'required|is_unique[kategori.nama_kategori]',
            'colour'        => 'required',
            'cover_image'   => 'max_size[cover_image,2048]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle Upload Cover Image jika ada yang diupload
        $coverFile = $this->request->getFile('cover_image');
        $coverName = null;
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/kategori', $coverName);
        }

        // Buat slug otomatis
        $slug = mb_url_title($this->request->getPost('nama_kategori'), '-', true);

        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'slug'          => $slug,
            'colour'        => $this->request->getPost('colour'),
            'cover_image'   => $coverName,
            'deskripsi'     => $this->request->getPost('deskripsi')
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Kategori baru berhasil ditambahkan!');
    }

    // 3. Tampilan Form Edit Kategori
    public function kategoriEdit($id)
    {
        if (!auth()->user()->inGroup('admin', 'developer')) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses.');
        }

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kategori tidak ditemukan.");
        }

        $data = [
            'title'    => 'Edit Kategori | Ruang Rikza',
            'kategori' => $kategori
        ];

        return view('dashboard/kategori/form', $data);
    }

    // 4. Proses Update Perubahan Kategori
    public function kategoriUpdate($id)
    {
        if (!auth()->user()->inGroup('admin', 'developer')) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses.');
        }

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kategori tidak ditemukan.");
        }

        $rules = [
            'nama_kategori' => "required|is_unique[kategori.nama_kategori,id,{$id}]",
            'colour'        => 'required',
            'cover_image'   => 'max_size[cover_image,2048]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle ganti Cover Image baru
        $coverFile = $this->request->getFile('cover_image');
        $coverName = $kategori['cover_image']; // Pakai nama file lama sebagai default

        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/kategori', $coverName);

            // Hapus file cover lama dari server jika ada dan file lamanya eksis
            if (!empty($kategori['cover_image']) && file_exists(FCPATH . 'uploads/kategori/' . $kategori['cover_image'])) {
                unlink(FCPATH . 'uploads/kategori/' . $kategori['cover_image']);
            }
        }

        $slug = mb_url_title($this->request->getPost('nama_kategori'), '-', true);

        $this->kategoriModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'slug'          => $slug,
            'colour'        => $this->request->getPost('colour'),
            'cover_image'   => $coverName,
            'deskripsi'     => $this->request->getPost('deskripsi')
        ]);

        return redirect()->to('/dashboard/artikel')->with('message', 'Kategori berhasil diperbarui!');
    }

    // 5. Proses Hapus Kategori
    public function kategoriHapus($id)
    {
        if (!auth()->user()->inGroup('admin', 'developer')) {
            return redirect()->to('/dashboard/artikel')->with('error', 'Anda tidak memiliki akses.');
        }

        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kategori tidak ditemukan.");
        }

        $this->kategoriModel->delete($id);

        return redirect()->to('/dashboard/artikel')->with('message', 'Kategori berhasil dihapus!');
    }
}