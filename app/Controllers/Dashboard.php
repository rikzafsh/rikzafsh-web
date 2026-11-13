<?php

namespace App\Controllers;

use App\Models\ArtikelModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = auth()->id();

        $totalArtikel = (new ArtikelModel())->where('penulis_id', $userId)->countAllResults();

        $queryViews = (new ArtikelModel())->selectSum('views')->where('penulis_id', $userId)->first();
        $totalViews = (int) ($queryViews['views'] ?? 0);

        $totalDraf = 0;
        if (ArtikelModel::adaKolomStatus()) {
            $totalDraf = (new ArtikelModel())->where('penulis_id', $userId)->where('status', 'draft')->countAllResults();
        }

        $totalKomentar = \Config\Database::connect()->table('komentar')
            ->join('artikel', 'artikel.id = komentar.artikel_id')
            ->where('artikel.penulis_id', $userId)
            ->countAllResults();

        $artikelTerbaru = (new ArtikelModel())->where('penulis_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        return view('dashboard/index', [
            'title'           => 'Dashboard | Ruang Rikza',
            'total_artikel'   => $totalArtikel,
            'total_views'     => $totalViews,
            'total_draf'      => $totalDraf,
            'total_komentar'  => $totalKomentar,
            'artikel_terbaru' => $artikelTerbaru,
        ]);
    }

    public function profil()
    {
        $data = [
            'title' => 'Profil Saya | Ruang Rikza',
            'user'  => auth()->user() // Mengambil data user dari Shield
        ];

        return view('dashboard/profil', $data);
    }

    // TAMBAHKAN FUNGSI INI UNTUK PROSES UPDATE
    public function profilUpdate()
    {
        $users = auth()->getProvider();
        $user  = auth()->user();

        // Aturan validasi input teks, bio, dan foto profil
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[70]',
            'bio'       => 'permit_empty|max_length[160]',
            'avatar'    => 'permit_empty|is_image[avatar]|max_size[avatar,2048]|mime_in[avatar,image/jpg,image/jpeg,image/png]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Set data input teks ke object user
        $user->full_name = $this->request->getPost('full_name');
        $user->bio       = $this->request->getPost('bio');

        // Handle upload file foto profil
        $avatarFile = $this->request->getFile('avatar');
        
        if ($avatarFile && $avatarFile->isValid() && ! $avatarFile->hasMoved()) {
            // Hapus foto lama jika ada di server agar hemat penyimpanan
            if (!empty($user->avatar) && file_exists(FCPATH . 'uploads/avatars/' . $user->avatar)) {
                unlink(FCPATH . 'uploads/avatars/' . $user->avatar);
            }

            // Berikan nama acak baru yang aman
            $newAvatarName = $avatarFile->getRandomName();
            
            // Pindahkan file ke public/uploads/avatars/
            $avatarFile->move(FCPATH . 'uploads/avatars', $newAvatarName);
            
            // Simpan nama file ke database
            $user->avatar = $newAvatarName;
        }

        // Simpan perubahan data ke database via Shield
        $users->save($user);

        return redirect()->to('/dashboard/profil')->with('message', 'Profil Anda berhasil diperbarui!');
    }

    public function manageUsers()
    {
        // Cek apakah yang login benar-benar developer
        if (! auth()->user()->inGroup('developer')) {
            return redirect()->to('/dashboard')->with('error', 'Hanya Developer yang memiliki akses ke halaman ini.');
        }

        $userModel = auth()->getProvider();
        
        $data = [
            'title' => 'Kelola Pengguna | Ruang Rikza',
            'users' => $userModel->orderBy('created_at', 'DESC')->findAll()
        ];

        return view('dashboard/users/index', $data);
    }

    public function updateUserRole($userId)
    {
        if (! auth()->user()->inGroup('developer')) {
            return redirect()->to('/dashboard')->with('error', 'Aksi tidak diizinkan.');
        }

        $roleValid = ['developer', 'admin', 'superuser', 'user'];
        $newRole   = $this->request->getPost('role');

        if (! in_array($newRole, $roleValid, true)) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }

        // Mencegah developer mengunci dirinya sendiri dari sistem
        if ((int) $userId === (int) auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $userModel  = auth()->getProvider();
        $targetUser = $userModel->find($userId);

        if (! $targetUser) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Hapus semua role lama agar tidak tumpang tindih, lalu pasang role baru
        $targetUser->removeGroup(...$roleValid);
        $targetUser->addGroup($newRole);

        return redirect()->back()->with('message', 'Role ' . $targetUser->username . ' berhasil diubah menjadi ' . $newRole);
    }
}
