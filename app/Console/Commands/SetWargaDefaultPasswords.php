<?php

namespace App\Console\Commands;

use App\Models\DataWarga;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetWargaDefaultPasswords extends Command
{
    protected $signature = 'parete:set-warga-passwords {--password=warga123}';

    protected $description = 'Set default password for warga without password hash';

    public function handle(): int
    {
        $password = (string) $this->option('password');
        $count = 0;

        DataWarga::query()->get()->each(function (DataWarga $warga) use ($password, &$count) {
            if (empty($warga->password)) {
                $warga->password = Hash::make($password);
                $warga->harus_ganti_password = true;
                $warga->save();
                $count++;
            }
        });

        $this->info("Updated {$count} warga record(s). Default password: {$password}");

        return self::SUCCESS;
    }
}
