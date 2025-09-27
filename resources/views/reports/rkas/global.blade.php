@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">

    <!-- Header Laporan -->
    <h1 class="text-4xl font-extrabold mb-2 text-gray-900">
        Laporan Keuangan Global RKAS
    </h1>
    <p class="text-xl text-indigo-600 mb-6 font-semibold">
        {{ $school->name ?? 'Nama Sekolah' }}
    </p>
    
    <!-- Informasi Periode Aktif -->
    @if ($period)
    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-lg shadow-sm mb-8">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <!-- Icon: Lucide Calendar -->
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 9h.01M9 16h.01M13 16h.01M17 16h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-indigo-800">
                    Periode Aktif: <span class="font-bold">{{ $period->name }}</span>
                    ({{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }})
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm mb-8">
        <p class="text-sm font-medium text-yellow-800">
            Perhatian: Tidak ada periode keuangan aktif ditemukan. Laporan mungkin kosong.
        </p>
    </div>
    @endif

    <!-- Tabel Ringkasan Sumber Kas -->
    <div class="overflow-x-auto shadow-xl rounded-xl">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Sumber Kas</th>
                    <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider">Saldo Awal</th>
                    <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider bg-green-700/80">Total Pemasukan</th>
                    <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider bg-red-700/80">Total Pengeluaran</th>
                    <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider">Saldo Akhir</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @php
                    $grandTotalInitial = 0;
                    $grandTotalIncome = 0;
                    $grandTotalExpense = 0;
                    $grandTotalFinal = 0;
                @endphp
                
                @forelse ($cashManagements as $item)
                    {{-- Asumsi $item memiliki properti: id, name, initial_balance_amount, total_income, total_expense, balance --}}
                    
                    @php
                        // Akumulasi Grand Total
                        $grandTotalInitial += $item->initial_balance_amount;
                        $grandTotalIncome += $item->total_income;
                        $grandTotalExpense += $item->total_expense;
                        $grandTotalFinal += $item->balance;
                    @endphp

                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                            Rp {{ number_format($item->initial_balance_amount, 0, ',', '.') }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                            Rp {{ number_format($item->total_income, 0, ',', '.') }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600">
                            Rp {{ number_format($item->total_expense, 0, ',', '.') }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $item->balance >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                            Rp {{ number_format($item->balance, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('school-.rkas.detail', ['school' => $school->id, 'cashManagement' => $item->id]) }}" 
                               class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out font-bold">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-lg text-gray-500">
                            Tidak ada sumber kas (Cash Management) yang terdaftar untuk periode aktif ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            <!-- Footer Grand Total -->
            <tfoot class="bg-gray-700 text-white">
                <tr>
                    <td class="px-6 py-4 text-left text-base font-extrabold uppercase">GRAND TOTAL</td>
                    
                    <td class="px-6 py-4 text-right text-base font-bold">
                        Rp {{ number_format($grandTotalInitial, 0, ',', '.') }}
                    </td>
                    
                    <td class="px-6 py-4 text-right text-base font-bold bg-green-700/80">
                        Rp {{ number_format($grandTotalIncome, 0, ',', '.') }}
                    </td>
                    
                    <td class="px-6 py-4 text-right text-base font-bold bg-red-700/80">
                        Rp {{ number_format($grandTotalExpense, 0, ',', '.') }}
                    </td>
                    
                    <td class="px-6 py-4 text-right text-xl font-extrabold {{ $grandTotalFinal >= 0 ? 'text-yellow-300' : 'text-pink-400' }}">
                        Rp {{ number_format($grandTotalFinal, 0, ',', '.') }}
                    </td>
                    
                    <td class="px-6 py-4"></td> <!-- Kolom Aksi Kosong -->
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
