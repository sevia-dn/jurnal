<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $isPiketActive = $user ? $user->isPiketActive() : false;
        $isWaka = $user ? $user->isWaka() : false;

        $pendingDispensasis = [];
        if ($isWaka) {
            $pendingDispensasis = Dispensasi::with(['siswa', 'pembuat'])
                ->where('status_waka', 'menunggu')
                ->latest()
                ->get();
        }

        $allDispensasis = Dispensasi::with(['siswa', 'pembuat', 'pemroses'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'isPiketActive',
            'isWaka',
            'pendingDispensasis',
            'allDispensasis'
        )); 
    }
}
