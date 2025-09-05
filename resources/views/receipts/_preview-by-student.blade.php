@foreach($receivables as $date => $items)
    <h5 class="mt-3">Tanggal Pembayaran: {{ $date }}</h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Jenis Tagihan</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>Rp {{ number_format($item->amount, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="table-light">
                <td><strong>Total</strong></td>
                <td><strong>Rp {{ number_format($items->sum('amount'), 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    <a href="{{ route('school-student-receipts.printByStudentAndDate', [$school, $student, $date]) }}" target="_blank" class="btn btn-success btn-sm">
        Cetak Kwitansi
    </a>
    <hr>
@endforeach