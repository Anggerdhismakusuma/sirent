<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
// Ganti Rental dengan model transaksi/rental yang digunakan di project.
use App\Models\RentalRequest;

class AboutController extends Controller
{
    public function index()
    {
        $stats = [
            [
                'label' => 'Barang tersedia untuk disewa',
                // 'value' => Product::where('status', 'active')->count(),
                'value' => "10000",
                'suffix' => '+',
            ],
            [
                'label' => 'Toko aktif di SI-RENT',
                // 'value' => User::where('role', User::ROLE_OWNER)
                //     ->where('is_owner_active', true)
                //     ->count(),
                'value' => "500",
                'suffix' => '+',
            ],
            [
                'label' => 'Penyewaan berhasil diselesaikan',
                'value' => RentalRequest::where('status', 'completed')->count(),
                'suffix' => '+',
            ],
            [
                'label' => 'Pengguna terdaftar',
                // 'value' => User::count(),
                'value' => "5000",
                'suffix' => '+',
            ],
        ];

        return view('about', compact('stats'));
    }
}