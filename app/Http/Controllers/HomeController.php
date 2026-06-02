<?php

namespace App\Http\Controllers;

use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->orderBy('service_name')
            ->get();

        return view('home', compact('services'));
    }
}
