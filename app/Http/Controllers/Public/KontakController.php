<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KontakController extends Controller
{
    public function index(): View
    {
        return view('public.kontak.index');
    }
}
