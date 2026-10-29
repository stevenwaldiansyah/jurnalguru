<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class RunDailyBackup extends Command
{
    protected $signature = 'jurnalguru:backup';

    protected $description = 'Menjalankan pencadangan database harian JurnalGuru';

    public function handle(DatabaseBackupService $backupService): int
    {
        try {
            $log = $backupService->runBackup();
            $this->info('Backup berhasil: '.$log->file_name);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup gagal: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
