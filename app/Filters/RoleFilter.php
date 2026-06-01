<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // $session = session();
        
        // // Pengecekan apakah pengguna sudah login
        // if (!$session->get('isLoggedIn')) {
        //     return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        // }

        // // Ambil role dari session penguna
        // $userRole = $session->get('role'); // misal nilainya: admin, developer, superuser, atau member

        // $allowedRoles = ['developer', 'admin', 'superuser'];

        // if (!in_array($userRole, $allowedRoles)) {
        //     // Jika role tidak sesuai, lempar ke halaman 403 Forbidden atau ke Dashboard
        //     throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Anda tidak memiliki akses ke halaman ini.");
        // }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu diisi
    }
}