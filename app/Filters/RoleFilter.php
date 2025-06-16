<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // $arguments adalah array berisi role yang diizinkan, dikirim dari file Routes
        // Contoh: ['admin', 'supervisor']
        if ($arguments) {
            $userRole = session()->get('role');
            // Jika role user saat ini tidak ada di dalam daftar role yang diizinkan
            if (!in_array($userRole, $arguments)) {
                // Redirect ke halaman utama dengan pesan error
                return redirect()->to('/')->with('error', 'Anda tidak memiliki hak akses ke halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
