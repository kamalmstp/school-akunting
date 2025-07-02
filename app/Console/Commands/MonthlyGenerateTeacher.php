<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyGenerateTeacher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-generate-teacher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate receivable data guru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Mulai generate piutang guru: " . now());

        $teacherCount = Teacher::where('is_active', true)->count();
        $scheduleCount = Schedule::where([
            ['user_type', '=','Guru'],
            ['status', '=', true]
        ])->count();

        if ($teacherCount === 0) {
            $this->warn("Tidak ada data guru. Job dilewati.");
            \Log::info('[Piutang Guru] Dilewati karena teachers kosong pada ' . now());
            return Command::SUCCESS;
        }

        if ($scheduleCount === 0) {
            $this->warn("Tidak ada data schedules. Job dilewati.");
            \Log::info('[Piutang Guru] Dilewati karena schedules kosong pada ' . now());
            return Command::SUCCESS;
        }

        // Group schedule berdasarkan school_id
        $schedulesBySchool = Schedule::where('user_type', 'Guru')->where('status', true)
            ->get()->groupBy('school_id');

        if ($schedulesBySchool->isEmpty()) {
            $this->warn("Tidak ada schedule dengan school_id valid. Job dilewati.");
            \Log::info('[Piutang Guru] Dilewati karena tidak ada schedule yang cocok dengan school_id.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $now = Carbon::now();

            Teacher::chunk(200, function ($teachers) use ($schedules, $now) {
                foreach ($teachers as $teacher) {
                    if ($teacher->is_active) {
                        $schoolId = $teacher->school_id;
                        $schedules = $schedulesBySchool->get($schoolId);

                        if (!$schedules || $schedules->isEmpty()) {
                            continue; // Tidak ada jadwal untuk sekolah ini
                        }

                        foreach ($schedules as $schedule) {
                            $isMonthly = $schedule->schedule_type === 'Bulanan';

                            $query = DB::table('teacher_receivables')
                                ->where('teacher_id', $teacher->id)
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
                            $receivableId = DB::table('teacher_receivables')->insertGetId([
                                'teacher_id' => $teacher->id,
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
                                'description' => Account::find($schedule->account_id)->name . ' guru: ' . $teacher->name,
                                'debit' => $schedule->amount,
                                'credit' => 0,
                                'reference_type'=> 'App\Models\TeacherReceivable',
                                'reference_id'   => $receivableId,
                                'created_at'     => $now,
                                'updated_at'     => $now,
                            ]);

                            // 3. Insert transaction credit
                            DB::table('transactions')->insert([
                                'school_id' => $schoolId,
                                'account_id' => $schedule->income_account_id,
                                'date' => $now,
                                'description' => Account::find($schedule->account_id)->name . ' guru: ' . $teacher->name,
                                'debit' => 0,
                                'credit' => $schedule->amount,
                                'reference_type'=> 'App\Models\TeacherReceivable',
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
