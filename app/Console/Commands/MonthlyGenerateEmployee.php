<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use App\Models\Employee;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyGenerateEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-generate-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate receivable data karyawan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Mulai generate piutang karyawan: " . now());

        $employeeCount = Employee::where('is_active', true)->count();
        $scheduleCount = Schedule::where([
            ['user_type', '=','Karyawan'],
            ['status', '=', true]
        ])->count();

        if ($employeeCount === 0) {
            $this->warn("Tidak ada data karyawan. Job dilewati.");
            \Log::info('[Piutang Karyawan] Dilewati karena employees kosong pada ' . now());
            return Command::SUCCESS;
        }

        if ($scheduleCount === 0) {
            $this->warn("Tidak ada data schedules. Job dilewati.");
            \Log::info('[Piutang Karyawan] Dilewati karena schedules kosong pada ' . now());
            return Command::SUCCESS;
        }

        // Group schedule berdasarkan school_id
        $schedulesBySchool = Schedule::where('user_type', 'Karyawan')->where('status', true)
            ->get()->groupBy('school_id');

        if ($schedulesBySchool->isEmpty()) {
            $this->warn("Tidak ada schedule dengan school_id valid. Job dilewati.");
            \Log::info('[Piutang Karyawan] Dilewati karena tidak ada schedule yang cocok dengan school_id.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $now = Carbon::now();

            Employee::chunk(200, function ($employees) use ($schedules, $now) {
                foreach ($employees as $employee) {
                    if ($employee->is_active) {
                        $schoolId = $employee->school_id;
                        $schedules = $schedulesBySchool->get($schoolId);

                        if (!$schedules || $schedules->isEmpty()) {
                            continue; // Tidak ada jadwal untuk sekolah ini
                        }

                        foreach ($schedules as $schedule) {
                            $isMonthly = $schedule->schedule_type === 'Bulanan';

                            $query = DB::table('employee_receivables')
                                ->where('employee_id', $employee->id)
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
                            $receivableId = DB::table('employee_receivables')->insertGetId([
                                'employee_id' => $employee->id,
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
                                'description' => Account::find($schedule->account_id)->name . ' karyawan: ' . $employee->name,
                                'debit' => $schedule->amount,
                                'credit' => 0,
                                'reference_type'=> 'App\Models\EmployeeReceivable',
                                'reference_id'   => $receivableId,
                                'created_at'     => $now,
                                'updated_at'     => $now,
                            ]);

                            // 3. Insert transaction credit
                            DB::table('transactions')->insert([
                                'school_id' => $schoolId,
                                'account_id' => $schedule->income_account_id,
                                'date' => $now,
                                'description' => Account::find($schedule->account_id)->name . ' karyawan: ' . $employee->name,
                                'debit' => 0,
                                'credit' => $schedule->amount,
                                'reference_type'=> 'App\Models\EmployeeReceivable',
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
