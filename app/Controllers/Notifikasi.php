<?php

namespace App\Controllers;

use App\Models\NotifikasiModel;

class Notifikasi extends BaseController
{
    /**
     * Daftar semua notifikasi milik pengguna yang login.
     */
    public function index()
    {
        $tersedia = NotifikasiModel::tersedia();
        $daftar   = [];
        $pager    = null;
        $belum    = 0;

        if ($tersedia) {
            $userId = (int) auth()->id();
            $model  = new NotifikasiModel();
            $daftar = $model->denganDetail($userId)->paginate(15);
            $pager  = $model->pager;
            $belum  = (new NotifikasiModel())->jumlahBelumDibaca($userId);
        }

        return view('notifikasi/index', [
            'title'        => 'Notifikasi | Ruang Rikza',
            'tersedia'     => $tersedia,
            'daftar'       => $daftar,
            'pager'        => $pager,
            'belum_dibaca' => $belum,
        ]);
    }

    /**
     * Membuka satu notifikasi: menandainya sudah dibaca lalu menuju halaman terkait.
     */
    public function buka($id)
    {
        if (! NotifikasiModel::tersedia()) {
            return redirect()->to('/');
        }

        $model = new NotifikasiModel();
        $n     = $model->denganDetail((int) auth()->id())->where('notifikasi.id', (int) $id)->first();

        if (! $n) {
            return redirect()->to('/notifikasi')->with('error', 'Notifikasi tidak ditemukan.');
        }

        if (! (int) $n['dibaca']) {
            \Config\Database::connect()->table('notifikasi')
                ->where('id', (int) $n['id'])
                ->where('user_id', (int) auth()->id())
                ->update(['dibaca' => 1]);
        }

        return redirect()->to(NotifikasiModel::tujuan($n));
    }

    public function bacaSemua()
    {
        if (NotifikasiModel::tersedia()) {
            \Config\Database::connect()->table('notifikasi')
                ->where('user_id', (int) auth()->id())
                ->where('dibaca', 0)
                ->update(['dibaca' => 1]);
        }

        return redirect()->to('/notifikasi')->with('message', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function hapusDibaca()
    {
        if (NotifikasiModel::tersedia()) {
            \Config\Database::connect()->table('notifikasi')
                ->where('user_id', (int) auth()->id())
                ->where('dibaca', 1)
                ->delete();
        }

        return redirect()->to('/notifikasi')->with('message', 'Notifikasi yang sudah dibaca telah dihapus.');
    }
}
