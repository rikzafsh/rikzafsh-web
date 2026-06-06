<?php

namespace App\Controllers;

use App\Models\PustakaKaryaModel;
// Tambahkan model lain jika nanti ada Web Apps atau Desain Grafis

class Projects extends BaseController
{
    public function index()
    {
        $pustakaModel = new \App\Models\PustakaKaryaModel();
        $majalahRaw = $pustakaModel->orderBy('edisi', 'DESC')->findAll();
        $majalah = [];

        foreach ($majalahRaw as $item) {
            $item['edisi_romawi']     = $this->konversiRomawi($item['edisi']);
            
            // --- TAMBAHKAN KEDUANYA DI SINI ---
            $item['periode']          = $this->ekstrakPeriode($item['codename']);
            $item['periode_otomatis'] = $this->ekstrakPeriode($item['codename']);
            // ----------------------------------
            
            // --- GENERATE EMBED LINK OTOMATIS ---
            $linkAsli = $item['link'] ?? '';
            if (strpos($linkAsli, 'drive.google.com/file/d/') !== false) {
                $item['embed_link'] = preg_replace('/\/view(.*)$/', '/preview', $linkAsli);
            } else {
                $item['embed_link'] = $linkAsli;
            }
            
            $majalah[] = $item;
        }

        $data = [
            'title'   => 'Projects',
            'majalah' => $majalah,
        ];

        return view('projects/index', $data);
    }

    // --- Helper Extract Periode & Romawi ---
    
    private function konversiRomawi($angka) {
        $n = intval($angka); $res = '';
        $romans = ['M'=>1000,'CM'=>900,'D'=>500,'CD'=>400,'C'=>100,'XC'=>90,'L'=>50,'XL'=>40,'X'=>10,'IX'=>9,'V'=>5,'IV'=>4,'I'=>1];
        foreach ($romans as $roman => $number) {
            $matches = intval($n / $number); $res .= str_repeat($roman, $matches); $n = $n % $number;
        }
        return $res;
    }
    
    private function ekstrakPeriode($codename) {
        if (preg_match('/^(\d{4})T(\d)/i', $codename, $matches)) {
            $thn = $matches[1]; $trm = $matches[2];
            if($trm == '1') return "Jan–Apr " . $thn;
            if($trm == '2') return "May–Aug " . $thn;
            if($trm == '3') return "Sep–Dec " . $thn;
        }
        return "Umum";
    }
}