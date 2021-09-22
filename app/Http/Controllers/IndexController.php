<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.index', []);
    }

    public function showConvert(Request $request)
    {
        return view('pages.convert.index', []);
    }
}
