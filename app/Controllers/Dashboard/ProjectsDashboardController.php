<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\PustakaKaryaModel;
// use App\Models\WebAppsModel; // Nanti tambahkan model lain di sini

class ProjectsDashboardController extends BaseController
{
    public function index()
    {
        // --- 1. AMBIL DATA PUSTAKA KARYA ---
        $pustakaModel = new PustakaKaryaModel();
        $majalahRaw = $pustakaModel->orderBy('edisi', 'DESC')->findAll();
        $majalah = [];

        foreach ($majalahRaw as $item) {
            $item['edisi_romawi'] = $this->konversiRomawi($item['edisi']);
            $item['periode_teks'] = $this->ekstrakPeriode($item['codename']);
            $majalah[] = $item;
        }

        // --- 2. AMBIL DATA WEB APPS (Nanti) ---
        // $webAppsModel = new WebAppsModel();
        // $webapps = $webAppsModel->findAll();

        $data = [
            'title'   => 'Kelola Proyek | Dashboard',
            'majalah' => $majalah,
            // 'webapps' => $webapps, // Nanti dilempar ke View
        ];

        return view('dashboard/projects/index', $data);
    }

    // Helper Konversi Pustaka Karya (Bisa ditaruh di sini untuk render view)
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
            if($trm == '2') return "Mei–Agu " . $thn;
            if($trm == '3') return "Sep–Des " . $thn;
        }
        return "Umum";
    }
}