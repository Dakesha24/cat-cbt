<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('pages/home');
    }

    public function guide()
    {
        return view('pages/guide');
    }

    public function profile()
    {
        return view('pages/profile');
    }

    public function contact()
    {
        return view('pages/contact');
    }

    public function about()
    {
        return view('pages/about');
    }

    public function faq()
    {
        return view('pages/faq');
    }

    public function bantuan()
    {
        return view('pages/bantuan');
    }
}
