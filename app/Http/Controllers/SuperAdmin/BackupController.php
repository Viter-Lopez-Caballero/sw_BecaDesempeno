<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\BackupCompleted;
use App\Models\Backup;
use App\Models\BackupSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;

class BackupController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // INDEX – pantalla principal
    // ─────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $rows   = (int) $request->input('rows', 10);

        $query = Backup::with('creator')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('creator', fn($u) => $u->where('name', 'like', "%{$search}%"));
            })
            ->latest();

        $backups = $query->paginate($rows)->withQueryString();

        // Stats
        $lastBackup  = Backup::where('status', 'completed')->latest()->first();
        $totalCount  = Backup::where('status', 'completed')->count();
        $schedule    = BackupSchedule::getConfig();

        // Format audit rows
        $backups->getCollection()->transform(function ($b) {
            return [
                'id'          => $b->id,
                'action'      => $b->type === 'scheduled' ? 'Programación automática' : 'Respaldo manual',
                'user'        => $b->creator?->name ?? 'Administrador',
                'name'        => $b->name,
                'date'        => $b->created_at->format('d-m-Y H:i:s'),
                'status'      => $b->status,
                'result'      => $b->status === 'completed' ? 'Realizado' : ($b->status === 'failed' ? 'Error' : 'En progreso'),
                'size'        => $b->file_size_formatted,
                'file_path'   => $b->file_path,
                'type'        => $b->type,
            ];
        });

        return Inertia::render('SuperAdmin/Backup/Index', [
            'backups'    => $backups,
            'filters'    => compact('search', 'rows'),
            'stats'      => [
                'last_backup'  => $lastBackup?->created_at->format('d-m-Y H:i:s') ?? '—',
                'next_backup'  => $schedule->next_run_at?->format('d-m-Y H:i') ?? '—',
                'total'        => $totalCount,
            ],
            'schedule'   => [
                'frequency'           => $schedule->frequency,
                'run_time'            => substr($schedule->run_time, 0, 5),
                'email_notifications' => $schedule->email_notifications,
                'is_active'           => $schedule->is_active,
            ],
            'backupList' => Backup::where('status', 'completed')
                ->latest()
                ->get(['id', 'name', 'created_at'])
                ->map(fn($b) => [
                    'value' => $b->id,
                    'label' => $b->name . ' — ' . $b->created_at->format('d/m/Y H:i'),
                ]),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // STORE – ejecutar respaldo manual
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'encrypted'   => 'boolean',
        ]);

        /** @var \App\Models\Backup $backupRecord */
        $backupRecord = Backup::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'status'       => 'in_progress',
            'type'         => 'manual',
            'is_encrypted' => $request->boolean('encrypted'),
            'created_by'   => Auth::id(),
        ]);

        try {
            $filePath = $this->runMysqldump($request->name, $request->boolean('encrypted'));

            $backupRecord->update([
                'file_path'  => $filePath,
                'file_size'  => Storage::disk('local')->size($filePath),
                'status'     => 'completed',
            ]);

            // Send notification email if enabled
            $schedule = BackupSchedule::getConfig();
            if ($schedule->email_notifications) {
                $backupRecord->refresh();
                User::role('Super Admin')->each(fn ($u) => Mail::to($u)->queue(new BackupCompleted($backupRecord)));
            }

            return redirect()->route('superadmin.backup.index')
                ->with('success', 'Respaldo creado correctamente.');

        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            $backupRecord->update(['status' => 'failed']);

            return redirect()->route('superadmin.backup.index')
                ->with('error', 'Error al crear el respaldo: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // SCHEDULE – guardar configuración de programación
    // ─────────────────────────────────────────────────────────────────────

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'frequency'           => 'required|in:daily,weekly,monthly',
            'run_time'            => 'required|date_format:H:i',
            'email_notifications' => 'boolean',
            'is_active'           => 'boolean',
        ]);

        $schedule = BackupSchedule::getConfig();
        $schedule->fill([
            'frequency'           => $request->frequency,
            'run_time'            => $request->run_time . ':00',
            'email_notifications' => $request->boolean('email_notifications'),
            'is_active'           => $request->boolean('is_active'),
        ]);
        $schedule->next_run_at = $schedule->calculateNextRun();
        $schedule->save();

        return redirect()->route('superadmin.backup.index')
            ->with('success', 'Programación actualizada correctamente.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // RESTORE – restaurar desde un respaldo
    // ─────────────────────────────────────────────────────────────────────

    public function restore(Request $request)
    {
        $request->validate([
            'backup_id' => 'required|exists:backups,id',
        ]);

        $backup = Backup::findOrFail($request->backup_id);

        if (!$backup->file_path || !Storage::disk('local')->exists($backup->file_path)) {
            return redirect()->route('superadmin.backup.index')
                ->with('error', 'El archivo de respaldo no se encontró en el servidor.');
        }

        try {
            $dbConfig  = config('database.connections.' . config('database.default'));
            $host      = $dbConfig['host'];
            $port      = $dbConfig['port'] ?? 3306;
            $user      = $dbConfig['username'];
            $pass      = $dbConfig['password'];
            $dbName    = $dbConfig['database'];
            $filePath  = Storage::disk('local')->path($backup->file_path);
            $mysqlPath = $this->findMysqlClientBinary();

            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s < "%s" 2>&1',
                $mysqlPath, $host, $port, $user, escapeshellarg($pass), $dbName, $filePath
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \RuntimeException(implode("\n", $output));
            }

            Log::info('Database restored from backup: ' . $backup->name . ' by user ' . Auth::id());

            return redirect()->route('superadmin.backup.index')
                ->with('success', 'Base de datos restaurada correctamente desde "' . $backup->name . '".');

        } catch (\Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return redirect()->route('superadmin.backup.index')
                ->with('error', 'Error al restaurar la base de datos: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // DOWNLOAD – descargar archivo .sql
    // ─────────────────────────────────────────────────────────────────────

    public function download(Backup $backup)
    {
        if (!$backup->file_path || !Storage::disk('local')->exists($backup->file_path)) {
            return redirect()->route('superadmin.backup.index')
                ->with('error', 'El archivo de respaldo no se encontró.');
        }

        return Storage::disk('local')->download(
            $backup->file_path,
            Str::slug($backup->name) . '_' . $backup->created_at->format('Y-m-d_H-i') . '.sql'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // DESTROY – eliminar registro y archivo
    // ─────────────────────────────────────────────────────────────────────

    public function destroy(Backup $backup)
    {
        if ($backup->file_path && Storage::disk('local')->exists($backup->file_path)) {
            Storage::disk('local')->delete($backup->file_path);
        }
        $backup->delete();

        return redirect()->route('superadmin.backup.index')
            ->with('success', 'Respaldo eliminado correctamente.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // VIEW METHODS (GET pages)
    // ─────────────────────────────────────────────────────────────────────

    public function create()
    {
        return Inertia::render('SuperAdmin/Backup/Create');
    }

    public function editSchedule()
    {
        $schedule = BackupSchedule::getConfig();
        return Inertia::render('SuperAdmin/Backup/Edit', [
            'schedule' => [
                'frequency'           => $schedule->frequency,
                'run_time'            => substr($schedule->run_time, 0, 5),
                'email_notifications' => $schedule->email_notifications,
                'is_active'           => $schedule->is_active,
                'next_run_at'         => $schedule->next_run_at?->format('d-m-Y H:i') ?? null,
                'frequency_label'     => $schedule->frequency_label,
            ],
        ]);
    }

    public function showRestore()
    {
        $backupList = Backup::where('status', 'completed')
            ->latest()
            ->get(['id', 'name', 'created_at', 'file_size', 'type'])
            ->map(fn($b) => [
                'id'    => $b->id,
                'name'  => $b->name,
                'label' => $b->name . ' — ' . $b->created_at->format('d/m/Y H:i'),
                'date'  => $b->created_at->format('d/m/Y H:i'),
                'size'  => $b->file_size_formatted,
                'type'  => $b->type === 'scheduled' ? 'Automático' : 'Manual',
            ])
            ->all();

        return Inertia::render('SuperAdmin/Backup/Restore', compact('backupList'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Runs mysqldump and stores the .sql file.
     * Returns the storage-relative path.
     */
    private function runMysqldump(string $backupName, bool $encrypted = false): string
    {
        $dbConfig  = config('database.connections.' . config('database.default'));
        $host      = $dbConfig['host'];
        $port      = $dbConfig['port'] ?? 3306;
        $user      = $dbConfig['username'];
        $pass      = $dbConfig['password'];
        $dbName    = $dbConfig['database'];

        $dumpBin   = $this->findMysqldumpBinary();
        $dir       = 'backups';
        $filename  = date('Y_m_d_His') . '_' . Str::slug($backupName) . '.sql';
        $storagePath = $dir . '/' . $filename;
        $fullPath  = Storage::disk('local')->path($storagePath);

        // Ensure directory exists
        if (!Storage::disk('local')->exists($dir)) {
            Storage::disk('local')->makeDirectory($dir);
        }

        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password=%s %s > "%s" 2>&1',
            $dumpBin, $host, $port, $user, escapeshellarg($pass), $dbName, $fullPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('mysqldump falló (código ' . $returnCode . '): ' . implode(' | ', $output));
        }

        if (!file_exists($fullPath) || filesize($fullPath) === 0) {
            throw new \RuntimeException('El archivo de respaldo está vacío o no se creó correctamente.');
        }

        // ── Encrypt with openssl if requested ────────────────────────────
        if ($encrypted) {
            $encryptedPath    = $storagePath . '.enc';
            $encryptedFull    = Storage::disk('local')->path($encryptedPath);
            $encryptionKey    = config('app.key'); // uses APP_KEY

            $encCmd = sprintf(
                'openssl enc -aes-256-cbc -pbkdf2 -iter 100000 -in "%s" -out "%s" -pass pass:%s 2>&1',
                $fullPath,
                $encryptedFull,
                escapeshellarg($encryptionKey)
            );

            exec($encCmd, $encOutput, $encCode);

            if ($encCode !== 0) {
                throw new \RuntimeException('openssl falló al cifrar: ' . implode(' | ', $encOutput));
            }

            // Remove plain SQL, keep only encrypted file
            @unlink($fullPath);
            $storagePath = $encryptedPath;
        }

        return $storagePath;
    }

    /**
     * Locate mysqldump binary in common Laragon / system paths.
     */
    private function findMysqldumpBinary(): string
    {
        $candidates = [
            // Laragon common paths
            'D:/Lalo104lucky/Descargas/Aplicaciones/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysqldump.exe',
            'C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysqldump.exe',
            'C:/laragon/bin/mysql/mysql-8.0.29-winx64/bin/mysqldump.exe',
            // WAMP
            'C:/wamp64/bin/mysql/mysql8.0.31/bin/mysqldump.exe',
            // XAMPP
            'C:/xampp/mysql/bin/mysqldump.exe',
            // System PATH (Linux/Mac)
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        // Try to find dynamically in Laragon's mysql dir
        $laragonBase = 'D:/Lalo104lucky/Descargas/Aplicaciones/laragon/bin/mysql';
        if (is_dir($laragonBase)) {
            foreach (glob($laragonBase . '/*/bin/mysqldump.exe') as $found) {
                array_unshift($candidates, $found);
            }
        }

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Last resort: assume it's in PATH
        return 'mysqldump';
    }

    /**
     * Locate mysql client binary for restore.
     */
    private function findMysqlClientBinary(): string
    {
        $laragonBase = 'D:/Lalo104lucky/Descargas/Aplicaciones/laragon/bin/mysql';
        if (is_dir($laragonBase)) {
            foreach (glob($laragonBase . '/*/bin/mysql.exe') as $found) {
                if (file_exists($found)) return $found;
            }
        }

        $candidates = [
            'C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysql.exe',
            'C:/wamp64/bin/mysql/mysql8.0.31/bin/mysql.exe',
            'C:/xampp/mysql/bin/mysql.exe',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) return $path;
        }

        return 'mysql';
    }
}
