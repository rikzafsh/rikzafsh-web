<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Beranda | Ruang Rikza',
            // 'theme' => 'mourning'
        ];
        return view('home', $data);
    }
}