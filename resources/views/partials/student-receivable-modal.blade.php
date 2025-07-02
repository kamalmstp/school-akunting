<div class="table-responsive">
	<table class="table align-middle" style="min-width: max-content;">
	    <thead>
			<tr>
		    	<th>No</th>
				<th>Nama Siswa</th>
                <th>Deskripsi</th>
                <th>Jumlah Pembayaran</th>
                <th>Tanggal Pembayaran</th>
			</tr>
		</thead>
		<tbody>
			@forelse($details as $index => $receivable)
                <tr>
					<td>{{ $index + 1 }}</td>
                    <td>{{ $receivable->student_receivable->student->name }}</td>
                    <td>{{ $receivable->description }}</td>
                    <td>{{ number_format($receivable->amount, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($receivable->period)->format('d M Y') }}</td>
                </tr>
			@empty
				<tr>
					<td colspan="5">Belum ada pembayaran</td>
				</tr>										
			@endempty
        </tbody>
	</table>
    <div class="d-flex gap-4 mb-4">
        <span>Piutang : {{ number_format($totalReceivable->amount, 0, ',', '.') }}</span>
        <span>Terbayar : {{ number_format($totalReceivable->paid_amount, 0, ',', '.') }}</span>
        <span>Sisa Piutang : {{ number_format($totalReceivable->amount - $totalReceivable->paid_amount, 0, ',', '.') }}</span>
    </div>	
</div>