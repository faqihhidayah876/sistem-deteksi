<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MangoDiseaseController extends Controller
{
    public function index()
    {
        return view('detect');
    }
}
