<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        return view('landing'); // nanti kita ganti layout SB Admin/landing
    }

}
