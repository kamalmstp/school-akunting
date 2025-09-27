@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4">

    <!-- Header Laporan -->
    <h1 class="text-3xl font-bold mb-2 text-gray-800">{{ $title }}</h1>
    <p class="text-lg text-gray-600 mb-4">
        Periode: <span class="font-semibold">{{ $activePeriod->name }} ({{ \Carbon\Carbon::parse($activePeriod->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($activePeriod->end_date)->format('d/m/Y') }})</span>
    </p>

    <!-- Ringkasan Saldo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Saldo Awal -->
        <div class="bg-indigo-100 border-l-4 border-indigo-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-indigo-600">Saldo Awal</p>
            <p class="text-xl font-bold text-indigo-800">
                Rp {{ number_format($initialBalance, 0, ',', '.') }}
            </p>
        </div>
        
        <!-- Total Pemasukan -->
        <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-green-600">Total Pemasukan (Debit)</p>
            <p class="text-xl font-bold text-green-800">
                Rp {{ number_format($totalDebit, 0, ',', '.') }}
            </p>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-red-600">Total Pengeluaran (Kredit)</p>
            <p class="text-xl font-bold text-red-800">
                Rp {{ number_format($totalCredit, 0, ',', '.') }}
            </p>
        </div>
        
        <!-- Saldo Akhir -->
        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 rounded shadow">
            <p class="text-sm font-medium text-blue-600">Saldo Akhir</p>
            <p class="text-xl font-bold text-blue-800">
                Rp {{ number_format($finalBalance, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Tabel Detail Transaksi -->
    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[5%]">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[40%]">Uraian / Deskripsi</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Pemasukan (Debit)</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Pengeluaran (Kredit)</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">Saldo Berjalan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                @php
                    // Inisialisasi Saldo Berjalan dengan Saldo Awal
                    $runningBalance = $initialBalance;
                    $i = 1;
                @endphp
                
                <!-- Baris Saldo Awal -->
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">SALDO AWAL</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">-</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-700">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                </tr>

                {{-- Loop melalui item transaksi yang sudah diurutkan dan dipisahkan dari Controller --}}
                @forelse ($transactions as $item)
                    @php
                        // Hitung Saldo Berjalan: bertambah jika debit, berkurang jika credit
                        $runningBalance += $item['debit'] - $item['credit'];
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i++ }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item['description'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-700">
                            Rp {{ number_format($item['debit'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-700">
                            Rp {{ number_format($item['credit'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                            Rp {{ number_format($runningBalance, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada transaksi tercatat untuk sumber kas ini dalam periode aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-4 text-left text-sm font-bold text-gray-900" colspan="3">TOTAL</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-green-700">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-red-700">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-blue-700">Rp {{ number_format($finalBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('school-rkas.global', ['school' => $school->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            &larr; Kembali ke Ringkasan Global
        </a>
    </div>

</div>
@endsection
