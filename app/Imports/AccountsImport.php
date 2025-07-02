<?php

namespace App\Imports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $codePatterns = [
        'Aset Lancar' => '/^1-1[0-9]{0,5}(-[0-9]+)*$/',
        'Aset Tetap' => '/^1-2[0-9]{0,5}(-[0-9]+)*$/',
        'Kewajiban' => '/^2-[0-9]{0,6}(-[0-9]+)*$/',
        'Aset Neto' => '/^3-[0-9]{0,6}(-[0-9]+)*$/',
        'Pendapatan' => '/^4-[0-9]{0,6}(-[0-9]+)*$/',
        'Biaya' => '/^5-[0-9]{0,6}(-[0-9]+)*$/',
        'Investasi' => '/^7-[0-9]{0,6}(-[0-9]+)*$/'
    ];

    /**
     * Transform Excel row to Account model or update existing.
     */
    public function model(array $row)
    {
        $parent = null;
        if (!empty($row['parent_kode'])) {
            $parent = Account::where('code', $row['parent_kode'])
                ->first();
            if (!$parent) {
                throw new \Exception("Kode parent {$row['parent_kode']} tidak ditemukan");
            }
            if (!Str::startsWith($row['kode'], $row['parent_kode'])) {
                throw new \Exception("Kode {$row['kode']} harus dimulai dengan kode parent {$row['parent_kode']}.");
            }
        }

        $data = [
            'code' => $row['kode'],
            'name' => $row['nama'],
            'account_type' => $row['tipe_akun'],
            'normal_balance' => $row['saldo_normal'],
            'parent_id' => $parent ? $parent->id : null,
        ];

        // Cari akun berdasarkan code
        $account = Account::where('code', $row['kode'])->first();

        if ($account) {
            // Update jika akun sudah ada
            $account->update($data);
            return null; // Kembalikan null agar tidak membuat record baru
        }

        // Buat akun baru jika tidak ada
        return new Account($data);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'tipe_akun' => 'required|in:Aset Lancar,Aset Tetap,Kewajiban,Aset Neto,Pendapatan,Biaya',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'parent_kode' => 'nullable|exists:accounts,code',
            '*.kode' => function ($attribute, $value, $fail) {
                $accountType = request()->input('tipe_akun');
                if ($accountType && isset($this->codePatterns[$accountType]) && !preg_match($this->codePatterns[$accountType], $value)) {
                    $fail("Kode {$value} tidak sesuai dengan pola untuk tipe akun {$accountType}.");
                }
            },
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'tipe_akun.required' => 'Tipe Akun wajib diisi.',
            'saldo_normal.required' => 'Saldo Normal wajib diisi.',
            'parent_kode.exists' => 'Kode parent tidak ditemukan.',
        ];
    }

    /**
     * Prepare data for validation (check code uniqueness manually).
     */
    public function prepareForValidation($data, $index)
    {
        // Validasi kode unik hanya untuk akun baru
        $existingAccount = Account::where('code', $data['kode'])->exists();

        if ($existingAccount && $data['kode']) {
            // Jika kode ada, skip validasi unik karena akan diupdate
            return $data;
        }

        // Tambahkan validasi unik untuk kode baru
        validator($data, [
            'kode' => ['required', Rule::unique('accounts', 'code')],
        ])->validate();

        return $data;
    }
}