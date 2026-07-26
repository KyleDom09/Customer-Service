<?php

namespace App\Http\Controllers;

class SelfServiceController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            return view('admin-selfserviceportal');
        }

        return view('selfserviceportal');
    }
}