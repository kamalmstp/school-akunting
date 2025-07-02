<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Student;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate receivable data siswa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Mulai generate piutang siswa: " . now());

        $studentCount = Student::where('is_active', true)->count();
        $scheduleCount = Schedule::where('user_type', 'Siswa')->where('status', true)->count();

        if ($studentCount === 0) {
            $this->warn("Tidak ada data siswa. Job dilewati.");
            \Log::info('[Piutang Siswa] Dilewati karena students kosong pada ' . now());
            return Command::SUCCESS;
        }

        if ($scheduleCount === 0) {
            $this->warn("Tidak ada data schedules. Job dilewati.");
            \Log::info('[Piutang Siswa] Dilewati karena schedules kosong pada ' . now());
            return Command::SUCCESS;
        }

        // Group schedule berdasarkan school_id
        $schedulesBySchool = Schedule::where('user_type', 'Siswa')->where('status', true)
            ->get()->groupBy('school_id');

        if ($schedulesBySchool->isEmpty()) {
            $this->warn("Tidak ada schedule dengan school_id valid. Job dilewati.");
            \Log::info('[Piutang Siswa] Dilewati karena tidak ada schedule yang cocok dengan school_id.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $now = Carbon::now();

            Student::chunk(200, function ($students) use ($schedulesBySchool, $now) {
                foreach ($students as $student) {
                    if ($student->is_active) {
                        $schoolId = $student->school_id;
                        $schedules = $schedulesBySchool->get($schoolId);

                        if (!$schedules || $schedules->isEmpty()) {
                            continue; // Tidak ada jadwal untuk sekolah ini
                        }

                        foreach ($schedules as $schedule) {
                            $isMonthly = $schedule->schedule_type === 'Bulanan';

                            $query = DB::table('student_receivables')
                                ->where('student_id', $student->id)
                                ->where('account_id', $schedule->account_id);

                            if ($isMonthly) {
                                $query->whereMonth('created_at', $now->month)
                                    ->whereYear('created_at', $now->year);
                            }

                            $alreadyExists = $query->exists();

                            if ($alreadyExists) {
                                continue; // Lewati jika sudah dibuat (per bulan atau sekali saja)
                            }
                            
                            // 1. Insert piutang
                            $receivableId = DB::table('student_receivables')->insertGetId([
                                'student_id' => $student->id,
                                'account_id' => $schedule->account_id,
                                'school_id' => $schoolId,
                                'amount' => $schedule->amount,
                                'paid_amount' => 0,
                                'due_date' => $schedule->due_date,
                                'status' => 'Unpaid',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                            // 2. Insert transaction debit
                            DB::table('transactions')->insertGetId([
                                'school_id' => $schoolId,
                                'account_id' => $schedule->account_id,
                                'date' => $now,
                                'description' => Account::find($schedule->account_id)->name . ' siswa: ' . $student->name,
                                'debit' => $schedule->amount,
                                'credit' => 0,
                                'reference_type'=> 'App\Models\StudentReceivables',
                                'reference_id'   => $receivableId,
                                'created_at'     => $now,
                                'updated_at'     => $now,
                            ]);

                            // 3. Insert transaction credit
                            DB::table('transactions')->insert([
                                'school_id' => $schoolId,
                                'account_id' => $schedule->income_account_id,
                                'date' => $now,
                                'description' => Account::find($schedule->account_id)->name . ' siswa: ' . $student->name,
                                'debit' => 0,
                                'credit' => $schedule->amount,
                                'reference_type'=> 'App\Models\StudentReceivables',
                                'reference_id'   => $receivableId,
                                'created_at'     => $now,
                                'updated_at'     => $now,
                            ]);
                        }
                    }
                }
            });

            DB::commit();
            $this->info("Piutang dan transaksi berhasil digenerate.");
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("Gagal generate piutang full: " . $e->getMessage());
            $this->error("Gagal generate!");
        }

        return Command::SUCCESS;
    }
}
