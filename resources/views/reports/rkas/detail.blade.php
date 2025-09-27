@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4">

    <!-- Header Laporan -->
    <h1 class="text-3xl font-bold mb-2 text-gray-800">{{ $title }}</h1>
    <p class="text-lg text-gray-600 mb-4">
        Periode: <span class="font-semibold">{{ $period->name }} ({{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }})</span>
    </p>

    <!-- Ringkasan Saldo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Saldo Awal -->
        <div class="bg-indigo-100 border-l-4 border-indigo-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-indigo-600">Saldo Awal</p>
            <p class="text-2xl font-bold text-indigo-800">
                Rp {{ number_format($initialBalance, 0, ',', '.') }}
            </p>
        </div>
        
        <!-- Saldo Berjalan -->
        @php
            // Hitung total Debit dan Kredit dari transaksi
            $totalDebit = $transactions->sum('debit');
            $totalCredit = $transactions->sum('credit');
        @endphp
        
        <!-- Total Pemasukan -->
        <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-green-600">Total Pemasukan (Debit)</p>
            <p class="text-2xl font-bold text-green-800">
                Rp {{ number_format($totalDebit, 0, ',', '.') }}
            </p>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-red-600">Total Pengeluaran (Kredit)</p>
            <p class="text-2xl font-bold text-red-800">
                Rp {{ number_format($totalCredit, 0, ',', '.') }}
            </p>
        </div>
    </div>
    
    <!-- Total Saldo Akhir -->
    @php
        $finalBalance = $initialBalance + $totalDebit - $totalCredit;
    @endphp
    <div class="bg-gray-800 text-white p-6 rounded-lg shadow-xl mb-8">
        <div class="flex justify-between items-center">
            <p class="text-xl font-semibold">Saldo Akhir Saat Ini</p>
            <p class="text-3xl font-extrabold">
                Rp {{ number_format($finalBalance, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Tabel Detail Transaksi (Mutasi) -->
    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Detail Mutasi (Transaksi)</h2>
    
    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggaran</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pemasukan (Debit)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran (Kredit)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php 
                    $runningBalance = $initialBalance;
                @endphp
                
                <!-- Tampilkan Saldo Awal sebagai baris pertama -->
                <tr class="bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold" colspan="6">SALDO AWAL</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-indigo-700">
                        Rp {{ number_format($initialBalance, 0, ',', '.') }}
                    </td>
                </tr>

                <!-- Loop Transaksi -->
                @forelse ($transactions as $index => $transaction)
                    @php
                        // Hitung saldo berjalan
                        $runningBalance = $runningBalance + $transaction->debit - $transaction->credit;
                    @endphp
                    
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $transaction->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->budget->name ?? 'N/A' }}</td>
                        
                        <!-- Debit (Pemasukan) -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $transaction->debit > 0 ? 'text-green-600' : 'text-gray-500' }}">
                            @if ($transaction->debit > 0)
                                Rp {{ number_format($transaction->debit, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        
                        <!-- Kredit (Pengeluaran) -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $transaction->credit > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            @if ($transaction->credit > 0)
                                Rp {{ number_format($transaction->credit, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        
                        <!-- Saldo Berjalan -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-gray-900">
                            Rp {{ number_format($runningBalance, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada transaksi tercatat untuk sumber kas ini dalam periode aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 text-left text-sm font-bold text-gray-900" colspan="4">TOTAL</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-green-700">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-red-700">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">Rp {{ number_format($finalBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('school-.rkas.global', ['school' => $school->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            &larr; Kembali ke Ringkasan Global
        </a>
    </div>

</div>
@endsection
