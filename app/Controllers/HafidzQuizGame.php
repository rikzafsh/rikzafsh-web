<?php

namespace App\Controllers;

use App\Models\UsersHafidzquizProgressModel;
use App\Models\UsersHafidzquizXpModel;
use CodeIgniter\API\ResponseTrait;

class HafidzQuizGame extends BaseController
{
    use ResponseTrait;

    protected $progressModel;
    protected $xpModel;

    public function __construct()
    {
        $this->progressModel = new UsersHafidzquizProgressModel();
        $this->xpModel = new UsersHafidzquizXpModel();
    }

    /**
     * Membaca file thumn.json untuk mencari batas awal dan akhir ayat
     */
    private function getThumnBoundaries($targetHizb, $targetThumn)
    {
        // Pastikan kamu meletakkan thumn.json di folder public/data/
        $jsonPath = FCPATH . 'data/thumn.json';
        if (!file_exists($jsonPath)) {
            return null;
        }

        $thumnData = json_decode(file_get_contents($jsonPath), true);
        
        $currentIndex = -1;
        // Cari index thumn yang diminta
        foreach ($thumnData as $index => $t) {
            if ($t['hizb_number'] == $targetHizb && $t['thumn'] == $targetThumn) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === -1) return null;

        $currentThumn = $thumnData[$currentIndex];
        
        // Cek thumn berikutnya untuk mengetahui batas akhir ayat
        $nextThumn = isset($thumnData[$currentIndex + 1]) ? $thumnData[$currentIndex + 1] : null;

        $boundaries = [
            'start_surah' => $currentThumn['sura_number'],
            'start_ayah'  => $currentThumn['starting_aya'],
            'end_surah'   => $nextThumn ? $nextThumn['sura_number'] : $currentThumn['sura_number'],
            // Jika ada thumn selanjutnya dan surahnya sama, batas akhirnya adalah ayat sebelum thumn selanjutnya
            // Jika surahnya beda, JS di frontend yang harus menyesuaikan batas akhirnya
            'end_ayah'    => $nextThumn ? ($nextThumn['starting_aya'] - 1) : 286 // Asumsi maks jika di ujung Al-Baqarah/Qur'an
        ];

        return $boundaries;
    }

    /**
     * API untuk mengambil data awal (Peta/Map) saat User login
     * Endpoint: /HafidzQuizGame/getMapData
     */
    public function getMapData()
{
    $userId = auth()->id();
    if (!$userId) return $this->failUnauthorized('Silakan login terlebih dahulu');

    // 1. Ambil SEMUA Progress User & XP (Tanpa filter Hizb)
    // Agar saat user pindah-pindah tab Hizb di frontend, datanya sudah tersedia.
    $progress = $this->progressModel->where('user_id', $userId)->findAll();
    $totalXp = $this->xpModel->getTotalXp($userId);

    // 2. Kita HAPUS bagian pembacaan thumn.json di sini karena:
    // Frontend (JavaScript) sudah mengambil alih tugas menghitung batas ayat
    // secara lebih dinamis dan akurat menggunakan surah-list.json.

    return $this->respond([
        'status'   => 'success',
        'total_xp' => $totalXp,
        'progress' => $progress
    ]);
}

    /**
     * API untuk menerima hasil saat user selesai mengerjakan 10 soal
     * Endpoint: /HafidzQuizGame/submitResult
     */
    public function submitResult()
    {
        $session = session();
        $userId = auth()->id();

        if (!$userId) {
            return $this->failUnauthorized('Sesi habis, silakan login ulang.');
        }

        // Menerima payload JSON dari Frontend (JS)
        $json = $this->request->getJSON();

        if (!$json) {
            return $this->failValidationErrors('Data tidak valid');
        }

        $hizb    = $json->hizb_number;
        $thumn   = $json->thumn_number;
        $node    = $json->node_number;
        $subnode = $json->subnode_number;
        $score   = $json->score; // 0 - 100
        $time    = $json->time_taken_seconds; 

        // 1. Hitung XP Berdasarkan Bobot Waktu
        $timeMinutes = $time / 60;
        $bobotWaktu = 0.75;
        if ($timeMinutes < 1) $bobotWaktu = 3;
        else if ($timeMinutes <= 2) $bobotWaktu = 2;
        else if ($timeMinutes <= 5) $bobotWaktu = 1;

        // Asumsi $score adalah persentase benar (misal skor 80 berarti 8 soal benar)
        $jumlahBenar = round(($score / 100) * 10);
        $xpGained = ceil($jumlahBenar * $bobotWaktu);

        // 2. Simpan XP ke Ledger
        if ($xpGained > 0) {
            $this->xpModel->insert([
                'user_id'        => $userId,
                'hizb_number'    => $hizb,
                'thumn_number'   => $thumn,
                'node_number'    => $node,
                'subnode_number' => $subnode,
                'xp_gained'      => $xpGained,
                'created_at'     => date('Y-m-d H:i:s')
            ]);
        }

        // 3. Update Progress (Gembok Level)
        $existingProgress = $this->progressModel->where([
            'user_id'        => $userId,
            'hizb_number'    => $hizb,
            'thumn_number'   => $thumn,
            'node_number'    => $node,
            'subnode_number' => $subnode,
        ])->first();

        $isCompletedNow = ($score >= 80) ? 1 : 0;
        $isLevelUp = false;

        if (!$existingProgress) {
            $this->progressModel->insert([
                'user_id'        => $userId,
                'hizb_number'    => $hizb,
                'thumn_number'   => $thumn,
                'node_number'    => $node,
                'subnode_number' => $subnode,
                'highest_score'  => $score,
                'is_completed'   => $isCompletedNow,
                'updated_at'     => date('Y-m-d H:i:s')
            ]);
            $isLevelUp = ($isCompletedNow == 1);
        } else {
            $dataUpdate = [];
            if ($score > $existingProgress['highest_score']) {
                $dataUpdate['highest_score'] = $score;
            }
            if ($existingProgress['is_completed'] == 0 && $isCompletedNow == 1) {
                $dataUpdate['is_completed'] = 1;
                $isLevelUp = true; // Level terbuka!
            }

            if (!empty($dataUpdate)) {
                $dataUpdate['updated_at'] = date('Y-m-d H:i:s');
                $this->progressModel->update($existingProgress['id'], $dataUpdate);
            }
        }

        return $this->respond([
            'status'     => 'success',
            'xp_gained'  => $xpGained,
            'is_levelup' => $isLevelUp,
            'message'    => 'Hasil berhasil disimpan!'
        ]);
    }

    public function index()
    {
        // Menampilkan halaman peta yang ada di app/Views/hafidzquiz_map.php
        return view('HafidzQuiz/hafidzquiz_map');
    }

    // Fungsi untuk memuat halaman antarmuka kuis
    public function play()
    {
        // Menangkap parameter dari URL, contoh: /hafidzquiz/play?hizb=1&thumn=2&node=1
        $data = [
            'hizb'  => $this->request->getGet('hizb') ?? 1,
            'thumn' => $this->request->getGet('thumn') ?? 1,
            'node'  => $this->request->getGet('node') ?? 1,
        ];
        
        return view('HafidzQuiz/hafidzquiz_play', $data);
    }
}