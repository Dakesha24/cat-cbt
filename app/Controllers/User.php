<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class User extends Controller
{
    public function __construct()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('logged_in')) {
            header('Location: ' . base_url('login'));
            exit();
        }
    }

    public function dashboard()
    {
        return view('user/dashboard');
    }

}
