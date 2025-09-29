<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebarMenuScroll">
        <ul class="sidebar-menu">

            @if(auth()->user()->role === 'SuperAdmin')
                <li class="@if(Route::is('dashboard')) active current-page @endif">
                    <a href="{{ route('dashboard') }}">
                        <i class="bi bi-pie-chart"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="@if(Route::is('schools.index')) active current-page @endif">
                    <a href="{{ route('schools.index') }}">
                        <i class="bi bi-building"></i>
                        <span class="menu-text">Kelola Sekolah</span>
                    </a>
                </li>
                <li class="treeview master">
                    <a href="#!">
                        <i class="bi bi-window-stack"></i>
                        <span class="menu-text">Data Master</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('teachers.index') }}" class="m-first">Kelola Guru</a>
                        </li>
                        <li>
                            <a href="{{ route('employees.index') }}" class="m-second">Kelola Karyawan</a>
                        </li>
                        <li>
                            <a href="{{ route('school-majors.index') }}" class="m-third">Kelola Kelas</a>
                        </li>
                        <li>
                            <a href="{{ route('students.index') }}" class="m-fourth">Kelola Siswa</a>
                        </li>
                        <li>
                            <a href="{{ route('fund-managements.index') }}" class="m-fifth">Kelola Dana</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('schedules.index') }}" class="m-sixth">Kelola Pembayaran</a>
                        </li> -->
                    </ul>
                </li>
                <li class="@if(Route::is('accounts.index')) active current-page @endif">
                    <a href="{{ route('accounts.index') }}">
                        <i class="bi bi-list"></i>
                        <span class="menu-text">Kelola Akun</span>
                    </a>
                </li>
                <li class="treeview receivable">
                    <a href="#!">
                        <i class="bi bi-cash-coin"></i>
                        <span class="menu-text">Penerimaan</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('student-receivables.index') }}" class="p-first">Piutang Siswa</a>
                        </li>
                        <li>
                            <a href="{{ route('teacher-receivables.index') }}" class="p-second">Piutang Guru</a>
                        </li>
                        <li>
                            <a href="{{ route('employee-receivables.index') }}" class="p-third">Piutang Karyawan</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview alumni">
                    <a href="#!">
                        <i class="bi bi-mortarboard"></i>
                        <span class="menu-text">Alumni</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('student-alumni.index') }}" class="a-first">Alumni Siswa</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview transaction">
                    <a href="#!">
                        <i class="bi bi-cash"></i>
                        <span class="menu-text">Transaksi</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('transactions.index') }}" class="tr-first">Tunai</a>
                        </li>
                        <li class="tr-third">
                            <a href="{{ route('fixed-assets.index') }}" class="tr-second">Non Tunai</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview report">
                    <a href="#!">
                        <i class="bi bi-journal"></i>
                        <span class="menu-text">Laporan</span>
                    </a>
                    <ul class="treeview-menu">
                        <!-- <li>
                            <a href="{{ route('reports.beginning-balance') }}" class="rp-first">Saldo Awal</a>
                        </li> -->
                        <li>
                            <a href="{{ route('reports.general-journal') }}" class="rp-second">Jurnal Umum</a>
                        </li>
                        <li>
                            <a href="{{ route('reports.ledger') }}" class="rp-third">Buku Besar</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('reports.trial-balance-before') }}" class="rp-fourth">Neraca Saldo Awal</a>
                        </li> -->
                        <!--<li>-->
                        <!--	<a href="{{ route('reports.adjusting-entries') }}" class="rp-fifth">Jurnal Penyesuaian</a>-->
                        <!--</li>-->
                        <!-- <li>
                            <a href="{{ route('reports.trial-balance-after') }}" class="rp-sixth">Neraca Saldo Akhir</a>
                        </li> -->
                        <li>
                            <a href="{{ route('reports.financial-statements') }}" class="rp-seventh">Laporan Keuangan</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('reports.cash-reports') }}" class="rp-eighth">Laporan Kas</a>
                        </li> -->
                        <li>
                            <a href="{{ route('reports.rkas-global') }}" class="rp-nineth">Laporan RKAS</a>
                        </li>
                    </ul>
                </li>
                <li class="@if(Route::is('users.index')) active current-page @endif">
                    <a href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i>
                        <span class="menu-text">Kelola Pengguna</span>
                    </a>
                </li>

            @elseif(auth()->user()->role === 'AdminMonitor')
                <li class="@if(Route::is('dashboard')) active current-page @endif">
                    <a href="{{ route('dashboard') }}">
                        <i class="bi bi-pie-chart"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="@if(Route::is('schools.index')) active current-page @endif">
                    <a href="{{ route('schools.index') }}">
                        <i class="bi bi-building"></i>
                        <span class="menu-text">Kelola Sekolah</span>
                    </a>
                </li>
                <li class="treeview master">
                    <a href="#!">
                        <i class="bi bi-window-stack"></i>
                        <span class="menu-text">Data Master</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('teachers.index') }}" class="m-first">Kelola Guru</a>
                        </li>
                        <li>
                            <a href="{{ route('employees.index') }}" class="m-second">Kelola Karyawan</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('school-majors.index') }}" class="m-third">Kelola Kelas</a>
                        </li> -->
                        <li>
                            <a href="{{ route('students.index') }}" class="m-fourth">Kelola Siswa</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('fund-managements.index') }}" class="m-fifth">Kelola Dana</a>
                        </li> -->
                        <!-- <li>
                            <a href="{{ route('schedules.index') }}" class="m-sixth">Kelola Pembayaran</a>
                        </li> -->
                    </ul>
                </li>
                <li class="@if(Route::is('accounts.index')) active current-page @endif">
                    <a href="{{ route('accounts.index') }}">
                        <i class="bi bi-list"></i>
                        <span class="menu-text">Kelola Akun</span>
                    </a>
                </li>
                <li class="treeview receivable">
                    <a href="#!">
                        <i class="bi bi-cash-coin"></i>
                        <span class="menu-text">Penerimaan</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('student-receivables.index') }}" class="p-first">Piutang Siswa</a>
                        </li>
                        <li>
                            <a href="{{ route('teacher-receivables.index') }}" class="p-second">Piutang Guru</a>
                        </li>
                        <li>
                            <a href="{{ route('employee-receivables.index') }}" class="p-third">Piutang Karyawan</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview alumni">
                    <a href="#!">
                        <i class="bi bi-mortarboard"></i>
                        <span class="menu-text">Alumni</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('student-alumni.index') }}" class="a-first">Alumni Siswa</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview transaction">
                    <a href="#!">
                        <i class="bi bi-cash"></i>
                        <span class="menu-text">Transaksi</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('transactions.index') }}" class="tr-first">Tunai</a>
                        </li>
                        <li class="tr-third">
                            <a href="{{ route('fixed-assets.index') }}" class="tr-second">Non Tunai</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview report">
                    <a href="#!">
                        <i class="bi bi-journal"></i>
                        <span class="menu-text">Laporan</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('reports.beginning-balance') }}" class="rp-first">Saldo Awal</a>
                        </li>
                        <li>
                            <a href="{{ route('reports.general-journal') }}" class="rp-second">Jurnal Umum</a>
                        </li>
                        <li>
                            <a href="{{ route('reports.ledger') }}" class="rp-third">Buku Besar</a>
                        </li>
                        <li>
                            <a href="{{ route('reports.trial-balance-before') }}" class="rp-fourth">Neraca Saldo Awal</a>
                        </li>
                        <!--<li>-->
                        <!--	<a href="{{ route('reports.adjusting-entries') }}" class="rp-fifth">Jurnal Penyesuaian</a>-->
                        <!--</li>-->
                        <li>
                            <a href="{{ route('reports.trial-balance-after') }}" class="rp-sixth">Neraca Saldo Akhir</a>
                        </li>
                        <li>
                            <a href="{{ route('reports.financial-statements') }}" class="rp-seventh">Laporan Keuangan</a>
                        </li>
                        <li>
                            <a href="{{ route('reports.cash-reports') }}" class="rp-eighth">Laporan Kas</a>
                        </li>
                    </ul>
                </li>


            @elseif(auth()->user()->role === 'SchoolAdmin')
                <li class="@if(Route::is('dashboard.index')) active current-page @endif">
                    <a href="{{ route('dashboard.index', auth()->user()->school_id) }}">
                        <i class="bi bi-pie-chart"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="treeview master">
                    <a href="#!">
                        <i class="bi bi-window-stack"></i>
                        <span class="menu-text">Data Master</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('school-teachers.index', auth()->user()->school_id) }}" class="m-first">Kelola Guru</a>
                        </li>
                        <li>
                            <a href="{{ route('school-employees.index', auth()->user()->school_id) }}" class="m-second">Kelola Karyawan</a>
                        </li>
                        <li>
                            <a href="{{ route('school-school-majors.index', auth()->user()->school_id) }}" class="m-third">Kelola Kelas</a>
                        </li>
                        <li>
                            <a href="{{ route('school-students.index', auth()->user()->school_id) }}" class="m-fourth">Kelola Siswa</a>
                        </li>
                        <li>
                            <a href="{{ route('school-cash-managements.index', auth()->user()->school_id) }}" class="m-fifth">Kelola Dana</a>
                        </li>

                        <li>
                            <a href="{{ route('school-financial-periods.index', auth()->user()->school_id) }}" class="m-sixth">Kelola Periode</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('school-schedules.index', auth()->user()->school_id) }}" class="m-sixth">Kelola Pembayaran</a>
                        </li> -->
                    </ul>
                </li>
                <!-- todo -->
                <li class="@if(Route::is('school-accounts.index')) active current-page @endif">
                    <a href="{{ route('school-accounts.index', auth()->user()->school_id) }}">
                        <i class="bi bi-list"></i>
                        <span class="menu-text">Kelola Akun</span>
                    </a>
                </li>
                <li class="treeview receivable">
                    <a href="#!">
                        <i class="bi bi-cash-coin"></i>
                        <span class="menu-text">Penerimaan</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('school-student-receivables.index', auth()->user()->school_id) }}" class="p-first">Piutang Siswa</a>
                        </li>
                        <li>
                            <a href="{{ route('school-teacher-receivables.index', auth()->user()->school_id) }}" class="p-second">Piutang Guru</a>
                        </li>
                        <li>
                            <a href="{{ route('school-employee-receivables.index', auth()->user()->school_id) }}" class="p-third">Piutang Karyawan</a>
                        </li>
                        <li>
                            <a href="{{ route('school-student-receipts.filter', auth()->user()->school_id) }}" class="p-fourth">Cetak Kwitansi Siswa</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview alumni">
                    <a href="#!">
                        <i class="bi bi-mortarboard"></i>
                        <span class="menu-text">Alumni</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('school-student-alumni.index', auth()->user()->school_id) }}" class="a-first">Alumni Siswa</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview transaction">
                    <a href="#!">
                        <i class="bi bi-cash"></i>
                        <span class="menu-text">Transaksi</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="{{ route('school-transactions.index', auth()->user()->school_id) }}" class="tr-first">Tunai</a>
                        </li>
                        <li>
                            <a href="{{ route('school-fixed-assets.index', auth()->user()->school_id) }}" class="tr-second">Non Tunai</a>
                        </li>
                    </ul>
                </li>
                <li class="treeview report">
                    <a href="#!">
                        <i class="bi bi-journal"></i>
                        <span class="menu-text">Laporan</span>
                    </a>
                    <ul class="treeview-menu">
                        <!-- <li>
                            <a href="{{ route('school-reports.beginning-balance', auth()->user()->school_id) }}" class="rp-first">Saldo Awal</a>
                        </li> -->
                        <li>
                            <a href="{{ route('school-reports.general-journal', auth()->user()->school_id) }}" class="rp-second">Jurnal Umum</a>
                        </li>
                        <li>
                            <a href="{{ route('school-reports.ledger', auth()->user()->school_id) }}" class="rp-third">Buku Besar</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('school-reports.trial-balance-before', auth()->user()->school_id) }}" class="rp-fourth">Neraca Saldo Awal</a>
                        </li> -->
                        <!-- <li>
                            <a href="{{ route('school-reports.adjusting-entries', auth()->user()->school_id) }}" class="rp-fifth">Jurnal Penyesuaian</a>
                        </li> -->
                        <!-- <li>
                            <a href="{{ route('school-reports.trial-balance-after', auth()->user()->school_id) }}" class="rp-sixth">Neraca Saldo Akhir</a>
                        </li> -->
                        <li>
                            <a href="{{ route('school-reports.financial-statements', auth()->user()->school_id) }}" class="rp-seventh">Laporan Keuangan</a>
                        </li>
                        <!-- <li>
                            <a href="{{ route('school-reports.cash-reports', auth()->user()->school_id) }}" class="rp-eighth">Laporan Kas</a>
                        </li> -->
                        <li>
                            <a href="{{ route('school-reports.rkas-global', auth()->user()->school_id) }}" class="rp-nineth">Laporan RKAS</a>
                        </li>
                    </ul>
                </li>

                <!-- <li class="treeview report">
                    <a href="#!">
                        <i class="bi bi-journals"></i>
                        <span class="menu-text">RKAS</span>
                    </a>
                    <ul class="treeview-menu">
                        
                    </ul>
                </li> -->
            @endif

            <li class="@if(Route::is('users.profile')) active current-page @endif">
                <a href="{{ route('users.profile') }}">
                    <i class="bi bi-person-square"></i>
                    <span class="menu-text">Profil</span>
                </a>
            </li>
        </ul>
    </div>
</nav>