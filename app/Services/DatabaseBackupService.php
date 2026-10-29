<?php

namespace App\Services;

use App\Models\BackupLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class DatabaseBackupService
{
    public function backupDirectory(): string
    {
        $path = storage_path('app/backups');
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    public function runBackup(): BackupLog
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $fileName = "backup_{$driver}_{$timestamp}.sql";
        $fullPath = $this->backupDirectory().DIRECTORY_SEPARATOR.$fileName;

        try {
            if ($driver === 'sqlite') {
                $dbPath = config("database.connections.{$connection}.database");
                if (! File::exists($dbPath)) {
                    throw new RuntimeException('File database SQLite tidak ditemukan.');
                }
                File::copy($dbPath, $fullPath);
            } elseif ($driver === 'mysql') {
                $this->runMysqlDump($fullPath, $connection);
            } else {
                throw new RuntimeException("Driver database {$driver} belum didukung untuk backup.");
            }

            return BackupLog::create([
                'back_time' => now(),
                'status' => 'berhasil',
                'file_name' => $fileName,
            ]);
        } catch (\Throwable $e) {
            BackupLog::create([
                'back_time' => now(),
                'status' => 'gagal',
                'file_name' => null,
            ]);

            throw $e;
        }
    }

    public function restoreFromFileName(string $fileName): void
    {
        if (! preg_match('/^backup_[a-z]+_\d{4}-\d{2}-\d{2}_\d{6}\.sql$/', $fileName)) {
            throw new RuntimeException('Nama file backup tidak valid.');
        }

        $fullPath = $this->backupDirectory().DIRECTORY_SEPARATOR.$fileName;
        if (! File::exists($fullPath)) {
            throw new RuntimeException('File backup tidak ditemukan.');
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            $dbPath = config("database.connections.{$connection}.database");
            File::copy($fullPath, $dbPath);
        } elseif ($driver === 'mysql') {
            $this->runMysqlRestore($fullPath, $connection);
        } else {
            throw new RuntimeException("Driver database {$driver} belum didukung untuk restore.");
        }
    }

    /** @return list<string> */
    public function listBackupFiles(): array
    {
        $dir = $this->backupDirectory();
        if (! File::isDirectory($dir)) {
            return [];
        }

        return collect(File::files($dir))
            ->map(fn ($f) => $f->getFilename())
            ->sortDesc()
            ->values()
            ->all();
    }

    private function runMysqlDump(string $outputPath, string $connection): void
    {
        $cfg = config("database.connections.{$connection}");
        $dump = $this->findExecutable(['mysqldump', 'C:\\xampp\\mysql\\bin\\mysqldump.exe']);
        if (! $dump) {
            throw new RuntimeException('mysqldump tidak ditemukan. Pasang MySQL client atau gunakan SQLite untuk pengembangan.');
        }

        $result = Process::env([
            'MYSQL_PWD' => (string) ($cfg['password'] ?? ''),
        ])->run([
            $dump,
            '-h', (string) ($cfg['host'] ?? '127.0.0.1'),
            '-P', (string) ($cfg['port'] ?? '3306'),
            '-u', (string) ($cfg['username'] ?? 'root'),
            (string) $cfg['database'],
        ]);

        if (! $result->successful()) {
            throw new RuntimeException('Backup MySQL gagal: '.$result->errorOutput());
        }

        File::put($outputPath, $result->output());
    }

    private function runMysqlRestore(string $inputPath, string $connection): void
    {
        $cfg = config("database.connections.{$connection}");
        $mysql = $this->findExecutable(['mysql', 'C:\\xampp\\mysql\\bin\\mysql.exe']);
        if (! $mysql) {
            throw new RuntimeException('mysql client tidak ditemukan.');
        }

        $sql = File::get($inputPath);
        $result = Process::env([
            'MYSQL_PWD' => (string) ($cfg['password'] ?? ''),
        ])->input($sql)->run([
            $mysql,
            '-h', (string) ($cfg['host'] ?? '127.0.0.1'),
            '-P', (string) ($cfg['port'] ?? '3306'),
            '-u', (string) ($cfg['username'] ?? 'root'),
            (string) $cfg['database'],
        ]);

        if (! $result->successful()) {
            throw new RuntimeException('Restore MySQL gagal: '.$result->errorOutput());
        }
    }

    /** @param  list<string>  $candidates */
    private function findExecutable(array $candidates): ?string
    {
        foreach ($candidates as $bin) {
            if (str_contains($bin, '\\') && File::exists($bin)) {
                return $bin;
            }
            $which = Process::run(PHP_OS_FAMILY === 'Windows'
                ? ['where', $bin]
                : ['which', $bin]);
            if ($which->successful() && trim($which->output()) !== '') {
                return trim(explode("\n", $which->output())[0]);
            }
        }

        return null;
    }
}
