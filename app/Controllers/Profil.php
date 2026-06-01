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

        $artikelTerbaru = $artikelModel->where('penulis_id', $user->id) 
                                       ->orderBy('created_at', 'DESC')
                                       ->limit(2)
                                       ->findAll();

        $data = [
            'title'          => 'Profil ' . ($user->full_name ?? $user->username) . ' | Ruang Rikza',
            'user'           => $user,
            'artikelTerbaru' => $artikelTerbaru // Kirim data ke view
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        // 1. Ambil data user yang sedang login saat ini via Shield
        $users = auth()->getProvider();
        $user  = auth()->user();

        // 2. Siapkan aturan validasi
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[70]',
            'bio'       => 'permit_empty|max_length[160]',
            'avatar'    => 'permit_empty|is_image[avatar]|max_size[avatar,2048]|mime_in[avatar,image/jpg,image/jpeg,image/png]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 3. Set data teks dasar
        $user->full_name = $this->request->getPost('full_name');
        $user->bio       = $this->request->getPost('bio');

        // 4. Proses Upload Foto Profil jika ada file baru yang masuk
        $avatarFile = $this->request->getFile('avatar');
        
        if ($avatarFile && $avatarFile->isValid() && ! $avatarFile->hasMoved()) {
            // Hapus foto profil lama jika ada file fisiknya
            if (!empty($user->avatar) && file_exists(FCPATH . 'uploads/avatars/' . $user->avatar)) {
                unlink(FCPATH . 'uploads/avatars/' . $user->avatar);
            }

            // Generate nama acak baru yang aman
            $newAvatarName = $avatarFile->getRandomName();
            
            // Pindahkan ke folder public/uploads/avatars/
            $avatarFile->move(FCPATH . 'uploads/avatars', $newAvatarName);
            
            // Masukkan nama filenya ke data objek user
            $user->avatar = $newAvatarName;
        }

        // 5. Simpan perubahan ke database melalui Shield Provider
        $users->save($user);

        return redirect()->to('/dashboard/profil')->with('message', 'Profil Anda berhasil diperbarui!');
    }
}