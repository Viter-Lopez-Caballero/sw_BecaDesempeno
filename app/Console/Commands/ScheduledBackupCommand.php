<?php

namespace App\Console\Commands;

use App\Mail\BackupCompleted;
use App\Models\Backup;
use App\Models\BackupSchedule;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ScheduledBackupCommand extends Command
{
    protected $signature = 'backup:run-scheduled';

    protected $description = 'Run the scheduled database backup based on configured frequency';

    public function handle(): int
    {
        $schedule = BackupSchedule::getConfig();

        if (!$schedule->is_active) {
            $this->info('Scheduled backup is inactive — skipping.');
            return self::SUCCESS;
        }

        // Check if it is time to run
        if ($schedule->next_run_at && Carbon::now()->lt($schedule->next_run_at)) {
            $this->info('Not yet time to run scheduled backup. Next run: ' . $schedule->next_run_at);
            return self::SUCCESS;
        }

        $this->info('Running scheduled backup...');

        $backupName = 'Scheduled Backup';
        $backupRecord = Backup::create([
            'name'        => $backupName,
            'description' => 'Respaldo automático - ' . now()->format('d/m/Y H:i'),
            'status'      => 'in_progress',
            'type'        => 'scheduled',
            'created_by'  => null,
        ]);

        try {
            $dbConfig  = config('database.connections.' . config('database.default'));
            $host      = $dbConfig['host'];
            $port      = $dbConfig['port'] ?? 3306;
            $user      = $dbConfig['username'];
            $pass      = $dbConfig['password'];
            $dbName    = $dbConfig['database'];
            $dumpBin   = $this->findMysqldumpBinary();

            $dir       = 'backups';
            $filename  = date('Y_m_d_His') . '_scheduled.sql';
            $storagePath = $dir . '/' . $filename;
            $fullPath  = Storage::disk('local')->path($storagePath);

            if (!Storage::disk('local')->exists($dir)) {
                Storage::disk('local')->makeDirectory($dir);
            }

            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s > "%s" 2>&1',
                $dumpBin, $host, $port, $user, escapeshellarg($pass), $dbName, $fullPath
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \RuntimeException(implode(' | ', $output));
            }

            $backupRecord->update([
                'file_path'  => $storagePath,
                'file_size'  => file_exists($fullPath) ? filesize($fullPath) : null,
                'status'     => 'completed',
            ]);

            // Update last/next run timestamps
            $schedule->last_run_at = now();
            $schedule->next_run_at = $schedule->calculateNextRun();
            $schedule->save();

            // Send notification email if enabled
            if ($schedule->email_notifications) {
                $backupRecord->refresh();
                User::role('Super Admin')->each(fn ($u) => Mail::to($u)->queue(new BackupCompleted($backupRecord)));
            }

            $this->info('Scheduled backup completed: ' . $storagePath);
            Log::info('Scheduled backup completed: ' . $storagePath);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $backupRecord->update(['status' => 'failed']);
            Log::error('Scheduled backup failed: ' . $e->getMessage());
            $this->error('Scheduled backup failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    private function findMysqldumpBinary(): string
    {
        $laragonBase = 'D:/Lalo104lucky/Descargas/Aplicaciones/laragon/bin/mysql';
        if (is_dir($laragonBase)) {
            foreach (glob($laragonBase . '/*/bin/mysqldump.exe') as $found) {
                if (file_exists($found)) return $found;
            }
        }

        $candidates = [
            'C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysqldump.exe',
            'C:/wamp64/bin/mysql/mysql8.0.31/bin/mysqldump.exe',
            'C:/xampp/mysql/bin/mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) return $path;
        }

        return 'mysqldump';
    }
}
