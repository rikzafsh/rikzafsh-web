<?php

namespace App\Controllers;

use App\Libraries\QuranJsonParser;

class HafidzQuiz extends BaseController
{
    // Halaman Setup (Kuis Utama)
    public function index()
    {
        return view('hafidz_quiz_setup');
    }

    // Halaman Tempat Bermain Kuis
    public function play()
    {
        return view('hafidz_quiz_play');
    }

    // Menggantikan fungsi generate.php lama Anda
    public function generate()
    {
        header('Content-Type: application/json; charset=utf-8');

        $surah_id = $this->request->getGet('surah_id') ?? 1;
        $ayah_start = $this->request->getGet('ayah_start') ?? 1;
        $ayah_end = $this->request->getGet('ayah_end') ?? 7;
        $jumlah_soal_diminta = $this->request->getGet('jumlah') ?? 5;
        $jenis_soal_req = $this->request->getGet('type') ?? ['sambung', 'lengkapi'];

        $json_file = "{$surah_id}.json";
        
        if (!file_exists(FCPATH . "data/{$json_file}")) {
            return $this->response->setJSON(['error' => "Data surah tidak ditemukan."]);
        }

        try {
            // Memanggil Library yang sudah kita buat di Langkah 2
            $quran = new QuranJsonParser($json_file);
            $semua_ayat = $quran->data;

            $data_rentang = array_filter($semua_ayat, function($ayat) use ($ayah_start, $ayah_end) {
                return $ayat['ayah'] >= $ayah_start && $ayat['ayah'] <= $ayah_end;
            });
            $data_rentang = array_values($data_rentang);

            $max_soal = count($data_rentang);
            if ($max_soal === 0) {
                return $this->response->setJSON(['error' => "Tidak ada ayat dalam rentang."]);
            }
            
            $jumlah_soal = min($jumlah_soal_diminta, $max_soal); 
            $soal_generated = [];
            $ayat_untuk_soal = $data_rentang;

            // ... (Salin seluruh sisa logika FOR LOOP dari generate.php Anda ke sini tanpa perubahan) ...
            // Catatan: Pastikan fungsi toArabicNumber($number) disisipkan juga di dalam kelas atau file ini.

            // Di baris paling akhir ganti echo json_encode menjadi:
            return $this->response->setJSON($soal_generated);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}