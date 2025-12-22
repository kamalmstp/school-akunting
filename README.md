# School Accounting App

Requirements :

1. PHP ^8.1
2. Laravel ^10.0
3. Bootstrap 5.3



Revisi :

* Laporan
[v] Untuk Cetak PDF, dibuat agar preview sebelum dicetak
[v] Tanggal di bagian Signature dibuat otomatis dengan format Bulan Indonesia
[v] Nama Kepala Sekolah, Nama Ketua Majelis Dikdasmen, dan Bendahara di set masing masing sekolah

* Profile
[v] CRUD utk Kota, Nama Kepsek, Nama Ketua Majelis Dikdasmen, dan Bendahara masing masing sekolah

* Kwitansi
[x] Untuk Tampilan siswa UI diperbagus dengan datatables
[x] untuk filter tanggal masing masing siswa untuk pencetakan

* Data Master
[v] Menu Kelola siswa fitur hapus dihide
[x] Menu Kelola Dana (AdminMonitor)
[x] Menu Kelola Periode (AdminMonitor)

* Tambah Role Baru
[v] Role Pengawas mirip dg AdminMonitor

* Saldo Awal
[v] Memilah akun yang dapat diisi saldo awal
[v] Pagination, Search, Datatables di saldo awal

* Alumni
[!] Semua siswa muncul di alumni, harusnya dipisah untuk siswa yang lulus saja yang muncul di daftar alumni
[!] Untuk detail akun tiap siswa sebaiknya digabungkan saja berdasarkan jenis akun, dan nominal nya di jumlahkan saja, tetapi apakah dipisah untuk status terbayar dan tidak terbayar, atau dipisahkan, yang sudah terbayar digabungkan.
case : Apabila digabungkan per akun, bagaimana dengan detail tanggal jatuh tempo nya, dan keterangan nya
[!] untuk pembayaran alumni untuk pengambilan ijazah hanya perlu membayar sebagian saja, sisanya dianggap lunas, tetapi di pencatatan akuntansi nya dicatat sebagai piutang tak tertagih.
[!] Apakah perlu dibikin status untuk pengambilan ijazah nya


- Minta nya cepet untuk performa -> Next Js
- VPS
- Notifikasi nya WA Blast dan email

Reseller Software Solidworks. Jualan License
Untuk Costumer biasanya Perusahaan sama Kampus


Login :
- Untuk login logout setiap 30 menit
- Setiap Bulan, Waktu untuk pengingat pergantian password, ditambahkan notifikasi
- Untuk Level User ada Super Admin, Admin/Manager tiap area, Sales, User dan Customer (mobile).
- untuk login, log activity dibagi 2, log login dan log perubahan data. (IP - negara, Browser, Device)
* Level Manager
- bisa melihat log activity user bawahan nya

Alur Manual:
- Pertama kali pekerjaan dimulai dari Sales
- Setelah ACC Masuk ke Technical Support, Demo Ke perusahaan, dan Training. Sekaligus Menghandle ticket dari customer
- Ketika Tiket masuk, manager menentukan melempar ke Technical Support yang mana.

*Admin Monitor
[x] Penerimaan Piutang Siswa Kolom Akun muncul berulang
[x] Piutang Guru & Karyawan tidak bisa diakses
[x] Piutang Karyawan double juga
[x] Penerimaan Tunai Bagian Akun tidak muncul atau tidak bisa di pilih
[x] Buku Besar tipe akun mengulang data yang sama
[x] Buku Besar List Akun tidak muncul
[x] Cetak PDF Jurnal Umum tidak bisa
[x] Cetak buku Besar pdf Error

