<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function __construct(private DatabaseBackupService $backupService) {}

    public function index(): View
    {
        $logs = BackupLog::orderByDesc('back_time')->paginate(20);
        $files = $this->backupService->listBackupFiles();
        $lastSuccess = BackupLog::query()->where('status', 'berhasil')->orderByDesc('back_time')->first();

        return view('admin.backups.index', compact('logs', 'files', 'lastSuccess'));
    }

    public function store(): RedirectResponse
    {
        try {
            $this->backupService->runBackup();

            return back()->with('success', 'Backup berhasil dijalankan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup gagal: '.$e->getMessage());
        }
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'file_name' => ['required', 'string'],
            'confirm' => ['required', 'accepted'],
        ], [
            'confirm.accepted' => 'Anda harus mengonfirmasi pemulihan data.',
        ]);

        try {
            $this->backupService->restoreFromFileName($request->string('file_name')->toString());

            return back()->with('success', 'Restore berhasil. Periksa integritas data.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore gagal: '.$e->getMessage());
        }
    }
}
