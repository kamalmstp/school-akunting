@extends('layouts.app')

@section('content')
    <!-- App hero header starts -->
	<div class="app-hero-header d-flex align-items-start">

		<!-- Breadcrumb start -->
	    <ol class="breadcrumb">
			<li class="breadcrumb-item">
				<i class="bi bi-pie-chart lh-1"></i>
				<a href="{{ auth()->user()->role == 'SuperAdmin' ? route('dashboard') : route('dashboard.index', auth()->user()->school_id) }}" class="text-decoration-none">Dashboard</a>
			</li>
			<li class="breadcrumb-item" aria-current="page">Piutang Siswa - Tambah</li>
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
                            <h5 class="card-title">Tambah Piutang @if(auth()->user()->role == 'SchoolAdmin') - {{ $school->name }} @endif</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ auth()->user()->role == 'SuperAdmin' ? route('student-receivables.store') : route('school-student-receivables.store', $school) }}" method="POST">
                            @csrf
                            <div class="create-invoice-wrapper">
                                <div class="row gx-3">
                                    <div class="col-sm-6 col-12">
                                        {{-- === SEKOLAH (jika SuperAdmin) === --}}
                                        @if(auth()->user()->role == 'SuperAdmin')
                                            <div class="mb-3">
                                                <label for="school_id" class="form-label">Sekolah</label>
                                                <select name="school_id" class="form-select @error('school_id') is-invalid @enderror" id="school_id">
                                                    <option value="">Pilih Sekolah</option>
                                                    @foreach(\App\Models\School::pluck('name', 'id') as $key => $schoolName)
                                                        <option value="{{ $key }}">{{ $schoolName }}</option>
                                                    @endforeach
                                                </select>
                                                @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        @else
                                            <input type="hidden" name="school_id" id="school_id" value="{{auth()->user()->school_id}}">
                                        @endif

                                        {{-- === SISWA === --}}
                                        <div class="mb-3">
                                            <label for="student_id" class="form-label">Siswa</label>
                                            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id">
                                                <option value="">Pilih Siswa</option>
                                                @foreach($students as $student)
                                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id_number }})</option>
                                                @endforeach
                                            </select>
                                            @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- === BLOK PIUTANG BERULANG === --}}
                                        <div id="piutang-container">
                                            <div class="piutang-item border rounded p-3 mb-3 bg-light">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">Piutang #1</h6>
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-piutang" style="display:none;">
                                                        <i class="bi bi-x-lg"></i> Hapus
                                                    </button>
                                                </div>

                                                {{-- Akun Piutang --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Akun Piutang</label>
                                                    <select class="form-select" name="account_id[]">
                                                        <option value="">Pilih Akun Piutang</option>
                                                        @foreach($accounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Akun Pendapatan --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Akun Pendapatan</label>
                                                    <select class="form-select" name="income_account_id[]">
                                                        <option value="">Pilih Akun Pendapatan</option>
                                                        @foreach(\App\Models\Account::where('school_id', auth()->user()->school_id)->where('account_type', 'Pendapatan')->get() as $account)
                                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Jumlah --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Jumlah</label>
                                                    <input type="text" class="form-control angka" name="amount[]" placeholder="Masukkan nominal">
                                                </div>

                                                {{-- Potongan --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Potongan</label>
                                                    <input type="text" class="form-control" name="discount_label[]" placeholder="Misal: Anak Guru">
                                                    <select name="discount_percent[]" class="form-select mt-2" style="max-width: 120px;">
                                                        <option value="0">0%</option>
                                                        @foreach([5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100] as $percent)
                                                            <option value="{{ $percent }}">{{ $percent }}%</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Tanggal Jatuh Tempo --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal Jatuh Tempo</label>
                                                    <input type="date" class="form-control" name="due_date[]">
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-outline-primary mb-3" id="add-piutang">
                                            <i class="bi bi-plus-lg"></i> Tambah Piutang
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="text-start">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ auth()->user()->role == 'SuperAdmin' ? route('student-receivables.index') : route('school-student-receivables.index', $school) }}" class="btn btn-outline-success ms-1">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Right column: Info Pembayaran -->
            <div class="col-xl-4">
                <!-- <div class="position-sticky" style="top: 0px; z-index: 10;"> -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Detail Piutang</h5>
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
                    <div id="loading-spinner" class="text-center my-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="card mb-3" id="show_history" style="display:none">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Pembayaran Terakhir</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="info_pembayaran_masuk"></div>
                        </div>
                    </div>
                <!-- </div> -->
            </div>
        </div>
		<!-- Row end -->

	</div>
	<!-- App body ends -->
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            function getPaymentHistory(school_id, student_id, account_id) {
                if (!school_id || !student_id || !account_id) return;

                $.ajax({
                    type: 'POST',
                    url: '/student-receivables/payment-history/filter',
                    data: { school_id, student_id, account_id },
                    dataType: 'json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    beforeSend: function () {
                        $('#loading-spinner').show();
                        $('#info_pembayaran_masuk').html('');
                        $('#show_history').hide();
                    },
                    success: function (data) {
                        let html = '';
                        if (Array.isArray(data) && data.length > 0) {
                            html += '<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tanggal</th><th>Jumlah</th><th>Deskripsi</th></tr></thead><tbody>';
                            $.each(data, function (key, row) {
                                const date = new Date(row.period);
                                const formattedDate = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                                const formattedAmount = new Intl.NumberFormat('id-ID').format(row.amount);
                                html += `<tr><td>${formattedDate}</td><td>Rp${formattedAmount}</td><td>${row.description}</td></tr>`;
                            });
                            html += '</tbody></table></div>';
                            $('#info_pembayaran_masuk').html(html);
                            $('#show_history').show();
                        }
                        $('#loading-spinner').hide();
                    }
                });
            }

            $(document).on('change', 'select[name="account_id[]"]', function () {
                const school_id = $('#school_id').val();
                const student_id = $('#student_id').val();
                const account_id = $(this).val();
                getPaymentHistory(school_id, student_id, account_id);
            });

            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function hitungTotal() {
                let total = parseInt($('#amount').val().replaceAll('.', '')) || 0;
                let persen_bayar = $('#persen_bayar').val();
                let total_persen_bayar = (persen_bayar>0) ? total*(persen_bayar/100) : total;
                let totalPotongan = 0;
                let potonganList = [];

                $('[name="discount_percent[]"]').each(function (index) {
                    const persen = parseInt($(this).val()) || 0;
                    const label = $('[name="discount_label[]"]').eq(index).val() || 'Potongan ' + (index + 1);
                    const nilai = Math.round((persen / 100) * total_persen_bayar);
                    totalPotongan += nilai;

                    if (persen > 0) {
                        potonganList.push(`<li>${label} (${persen}%) = ${formatRupiah(nilai)}</li>`);
                    }
                });

                let totalBayar = total_persen_bayar - totalPotongan;
                if (totalBayar < 0) totalBayar = 0;

                // update info panel
                $('#info_biaya').text(formatRupiah(total_persen_bayar));
                $('#info_total').text(formatRupiah(totalBayar));
                $('#info_potongan').html(potonganList.length ? potonganList.join('') : '<li>-</li>');
                $('#total_potongan').val(totalPotongan);
                $('#total_bayar').val(totalBayar);
                $('#final_amount').val(totalBayar);
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

            $('#school_id,#student_id').on('change', function () {
                getPaymentHistory();
            });

            $('#account_id').on('change', function () {
                const selectedText = $(this).find('option:selected').text();
                $('#info_pembayaran').text(selectedText ? selectedText.split(' - ')[1] || selectedText : '-');
                hitungTotal();
                getPaymentHistory();
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
            $(document).on('change', '[name="discount_percent[]"], [name="discount_label[]"], #persen_bayar', function () {
                hitungTotal();
            });
            $('#income_account_id').on('change', function () {
                const selectedText = $(this).find('option:selected').text().toLowerCase();

                if (selectedText.includes('infaq siswa')) {
                    $('#persen_bayar').val(1);
                    $('#show_pembayaran').show();
                } else {
                    $('#persen_bayar').val(0);
                    $('#show_pembayaran').hide();
                }
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
			$('#month').select2();
			$('#year').select2();
            if(@json(auth()->user()->role == 'SuperAdmin')) {
                $('#school_id').select2();
            }
            $('#school_id').on('change', function () {
                const school = $(this).val();
				if (school) {
					$.ajax({
						type:'POST',
						url:'/student-receivables/student/filter',
						data: {school},
						dataType: 'json',
						headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						success:function(data){
							let options = '<option value="">Pilih Siswa</option>';
							$.each(data, function(key, value) {
								options += '<option value=' + value['id'] + '>' + value['name'] + ' (' + value['student_id_number'] + ')' + '</option>';
							});
							$('#student_id').empty();
							$('#student_id').append(options);	
						}
					});
				}
			})
		})

        // === MULTI PIUTANG HANDLER ===
$(document).ready(function () {
    let piutangIndex = 1;

    $('#add-piutang').click(function () {
        piutangIndex++;
        let newPiutang = $('.piutang-item:first').clone();
        newPiutang.find('input, select').val('');
        newPiutang.find('h6').text('Piutang #' + piutangIndex);
        newPiutang.find('.remove-piutang').show();
        $('#piutang-container').append(newPiutang);
    });

    $(document).on('click', '.remove-piutang', function () {
        $(this).closest('.piutang-item').remove();
        // Reindex titles
        $('#piutang-container .piutang-item').each(function (i) {
            $(this).find('h6').text('Piutang #' + (i + 1));
        });
    });

    // Format angka otomatis
    $(document).on('input', '.angka', function () {
        $(this).val(function (index, value) {
            return value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });
});
	</script>
@endsection