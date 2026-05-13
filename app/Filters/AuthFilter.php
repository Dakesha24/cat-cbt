<?php 
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        // Dapatkan segment pertama dari URL (admin/guru/siswa)
        $segment = $request->uri->getSegment(1);
        
        // Cek apakah role user sesuai dengan segment URL
        if ($session->get('role') !== $segment) {
            return redirect()->to(base_url('login'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}