@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1"></i>
				<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('dashboard') : route('dashboard.index', $school) }}" class="text-decoration-none">Dashboard</a>
			</li>
			<li class="breadcrumb-item" aria-current="page">Piutang Siswa - Edit</li>
		</ol>
		<!-- Breadcrumb end -->
	</div>
	<!-- App Hero header ends -->

	<!-- App body starts -->
	<div class="app-body">

		<!-- Row start -->
		<div class="row gx-3">
            <!-- Left column: Form -->
			<div class="col-xl-8">
				<div class="card mb-3">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Edit Piutang - {{ $school->name }}</h5>
						</div>
					</div>
					<div class="card-body">
                        <form action="{{ route('school-student-receivables.update', [$school, $student_receivable]) }}" method="POST">
                        @csrf
                        @method('PUT')
                            <div class="create-invoice-wrapper">
                                <!-- Row start -->
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="student_id" class="form-label">Siswa</label>
                                                    <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id">
                                                        <option value="">Pilih Siswa</option>
                                                        @foreach($students as $student)
                                                            <option value="{{ $student->id }}" {{ old('student_id', $student_receivable->student_id) == $student->id ? 'selected' : '' }}>
                                                                {{ $student->name }} ({{ $student->student_id_number }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('student_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="account_id" class="form-label">Akun Piutang</label>
                                                    <select class="form-select @error('account_id') is-invalid @enderror" id="account_id" name="account_id">
                                                        <option value="">Pilih Akun Piutang</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}" {{ @old('account_id', $student_receivable->account_id) == $account->id ? 'selected' : '' }}>
                                                                {{ $account->code }} - {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('account_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="income_account_id" class="form-label">Akun Pendapatan</label>
                                                    <select class="form-select @error('income_account_id') is-invalid @enderror" id="income_account_id" name="income_account_id">
                                                        <option value="">Pilih Akun Pendapatan</option>
                                                        @foreach(\App\Models\Account::where('account_type', 'Pendapatan')->get() as $account)
                                                            <option value="{{ $account->id }}" {{ @old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                                                                {{ $account->code }} - {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('income_account_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="amount" class="form-label">Jumlah</label>
                                                    <input type="text" class="form-control angka @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', number_format($student_receivable->amount, 0, ',', '.')) }}">
                                                    @error('amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Potongan</label>
                                            @php
                                                $discounts = $discounts ?? collect(); // fallback if not set
                                            @endphp

                                            @if ($discounts->isEmpty())
                                                {{-- Show one empty row as default --}}
                                                <div class="d-flex align-items-start gap-2 mb-2">
                                                    <input type="text" name="discount_label[]" class="form-control" value="{{ old('discount_label.0') }}" placeholder="Misal: Anak Guru">
                                                    <select name="discount_percent[]" class="form-select" style="max-width: 100px;">
                                                        <option value="0">0%</option>
                                                        @foreach([5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100] as $percent)
                                                            <option value="{{ $percent }}" {{ old('discount_percent.0') == $percent ? 'selected' : '' }}>
                                                                {{ $percent }}%
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-outline-secondary" id="add-discount">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </button>
                                                </div>
                                            @else
                                                @foreach ($discounts as $i => $discount)
                                                    <div class="d-flex align-items-start gap-2 mb-2">
                                                        <input type="text" name="discount_label[]" class="form-control" value="{{ old("discount_label.$i", $discount->label) }}" placeholder="Misal: Anak Guru">
                                                        <select name="discount_percent[]" class="form-select" style="max-width: 100px;">
                                                            <option value="0">0%</option>
                                                            @foreach([5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100] as $percent)
                                                                <option value="{{ $percent }}" {{ old("discount_percent.$i", $discount->percent) == $percent ? 'selected' : '' }}>
                                                                    {{ $percent }}%
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button" class="btn btn-outline-secondary" id="add-discount">
                                                            <i class="bi bi-plus-lg"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <div id="discount-list"></div>
                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <!-- Form group start -->
                                                <div class="mb-3">
                                                    <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $student_receivable->due_date ? \Carbon\Carbon::parse($student_receivable->due_date)->format('Y-m-d') : '') }}">
                                                    @error('due_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!-- Form group end -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-3">
                                <div class="col-12">
                                    <div class="text-start">
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('student-receivables.index') : route('school-student-receivables.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
                                    </div>
                                </div>
                            </div>
                        </form>
					</div>
				</div>
			</div>

            <!-- Right column: Info Pembayaran -->
            <div class="col-xl-4">
                <div class="position-sticky" style="top: 0px; z-index: 10;">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Info Pembayaran</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Pembayaran:</strong> <span id="info_pembayaran">-</span></p>
                            <p><strong>Biaya:</strong> <span id="info_biaya">Rp 0</span></p>
                            <p><strong>Potongan:</strong></p>
                            <ul class="ps-3" id="info_potongan">
                                <li>-</li>
                            </ul>
                            <hr>
                            <p class="fw-bold">Total Bayar: <span id="info_total">Rp 0</span></p>
                        </div>
                    </div>
                </div>
            </div>

		</div>
		<!-- Row end -->

	</div>
	<!-- App body ends -->
</div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            const selectedText = $('#account_id').find('option:selected').text();
            $('#info_pembayaran').text(selectedText ? selectedText.split(' - ')[1] || selectedText : '-');
            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function hitungTotal() {
                let total = parseInt($('#amount').val().replaceAll('.', '')) || 0;
                let totalPotongan = 0;
                let potonganList = [];

                $('[name="discount_percent[]"]').each(function (index) {
                    const persen = parseInt($(this).val()) || 0;
                    const label = $('[name="discount_label[]"]').eq(index).val() || 'Potongan ' + (index + 1);
                    const nilai = Math.round((persen / 100) * total);
                    totalPotongan += nilai;

                    if (persen > 0) {
                        potonganList.push(`<li>${label} (${persen}%) = ${formatRupiah(nilai)}</li>`);
                    }
                });

                let totalBayar = total - totalPotongan;
                if (totalBayar < 0) totalBayar = 0;

                // update info panel
                $('#info_biaya').text(formatRupiah(total));
                $('#info_total').text(formatRupiah(totalBayar));
                $('#info_potongan').html(potonganList.length ? potonganList.join('') : '<li>-</li>');
                $('#total_potongan').val(totalPotongan);
                $('#total_bayar').val(totalBayar);
            }

            $('#add-discount').on('click', function () {
                const html = `
                    <div class="d-flex align-items-start gap-2 mb-2">
                    <input type="text" name="discount_label[]" class="form-control" placeholder="Misal: Prestasi">
                    <select name="discount_percent[]" class="form-select" style="max-width: 100px;">
                    <option value="0">0%</option>
                    @foreach([5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100] as $percent)
                    <option value="{{ $percent }}">{{ $percent }}%</option>
                    @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-danger remove-discount">
                    <i class="bi bi-x-lg"></i>
                    </button>
                    </div>
                `;
                $('#discount-list').append(html);
                hitungTotal();
            });

            $('#account_id').on('change', function () {
                const selectedText = $(this).find('option:selected').text();
                $('#info_pembayaran').text(selectedText ? selectedText.split(' - ')[1] || selectedText : '-');
                hitungTotal();
            });

            $(document).on('click', '.remove-discount', function () {
                $(this).closest('.d-flex').remove();
                hitungTotal();
            });

            // Hitung ulang jika nominal amount berubah
            $('#amount').on('input', function () {
                $(this).val(function (index, value) {
                    return value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
                hitungTotal();
            });
            $(document).on('change', '[name="discount_percent[]"], [name="discount_label[]"]', function () {
                hitungTotal();
            });

            // Inisialisasi awal jika amount sudah terisi
            hitungTotal();
        });
    </script>

	<script>
		$(document).ready(function(){
			$('#student_id').select2();
			$('#account_id').select2();
			$('#income_account_id').select2();
		})
	</script>
@endsection