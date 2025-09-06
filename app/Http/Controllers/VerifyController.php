<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;

class VerifyController extends Controller
{
    public function verify($code)
    {
        $receipt = Receipt::with(['student', 'school'])
            ->where('token', $code)
            ->first();

        if (!$receipt) {
            return view('receipts.verify', [
                'status' => 'error',
                'message' => 'Kwitansi tidak ditemukan atau kode salah.'
            ]);
        }

        return view('receipts.verify', [
            'status' => 'success',
            'receipt' => $receipt,
        ]);
    }
}
