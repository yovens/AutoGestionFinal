<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Backup baz done a an PHP pi (pa bezwen mysqldump): mache sou MySQL/MariaDB ak SQLite,
 * Windows kou Linux. Fichye a se yon .sql.gz: chak enstriksyon fini ak liy "-- @@".
 */
class Backup
{
    /** Tab sa yo: estrikti sèlman (done tanporè). */
    private const SKIP_DATA = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

    private const DELIMITER = "\n-- @@\n";

    public static function dir(): string
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        return $dir;
    }

    /** Kreye yon backup konplè. Retounen chemen fichye a. */
    public static function create(): string
    {
        $conn = DB::connection();
        $driver = $conn->getDriverName();
        $pdo = $conn->getPdo();

        if (!in_array($driver, ['mysql', 'mariadb', 'sqlite'], true)) {
            throw new RuntimeException("Base de données « {$driver} » non prise en charge par la sauvegarde.");
        }

        $path = self::dir() . '/autogestion-' . now()->format('Y-m-d-His') . '.sql.gz';
        $tmp = $path . '.part';
        $gz = gzopen($tmp, 'w6');

        $write = fn (string $sql) => gzwrite($gz, $sql . self::DELIMITER);

        gzwrite($gz, "-- Sauvegarde AutoGestion du " . now()->format('d/m/Y H:i:s') . " ({$driver})\n");
        gzwrite($gz, "-- Restauration : php artisan app:restore " . basename($path) . "\n\n");

        $write($driver === 'sqlite' ? 'PRAGMA foreign_keys=OFF' : 'SET FOREIGN_KEY_CHECKS=0');

        foreach (self::tables($driver) as $table => $createSql) {
            $q = $driver === 'sqlite' ? "\"{$table}\"" : "`{$table}`";
            $write("DROP TABLE IF EXISTS {$q}");
            $write($createSql);

            if (in_array($table, self::SKIP_DATA, true)) {
                continue;
            }

            $columns = null;
            foreach (DB::table($table)->cursor()->chunk(200) as $chunk) {
                $rows = [];
                foreach ($chunk as $row) {
                    $row = (array) $row;
                    $columns ??= implode(', ', array_map(fn ($c) => $driver === 'sqlite' ? "\"{$c}\"" : "`{$c}`", array_keys($row)));
                    $rows[] = '(' . implode(', ', array_map(fn ($v) => match (true) {
                        $v === null       => 'NULL',
                        is_bool($v)       => $v ? '1' : '0',
                        is_int($v), is_float($v) => (string) $v,
                        default           => $pdo->quote((string) $v),
                    }, $row)) . ')';
                }
                if ($rows) {
                    $write("INSERT INTO {$q} ({$columns}) VALUES\n" . implode(",\n", $rows));
                }
            }
        }

        $write($driver === 'sqlite' ? 'PRAGMA foreign_keys=ON' : 'SET FOREIGN_KEY_CHECKS=1');
        gzclose($gz);
        rename($tmp, $path);

        return $path;
    }

    /** Restore yon backup: ranplase TOUT done aktyèl yo. */
    public static function restore(string $file): int
    {
        $path = self::path($file);
        $sql = gzdecode((string) file_get_contents($path));

        if ($sql === false) {
            throw new RuntimeException('Fichier de sauvegarde illisible.');
        }

        $count = 0;
        foreach (explode(self::DELIMITER, $sql) as $statement) {
            $statement = trim(preg_replace('/^--[^\n]*\n/m', '', $statement));
            if ($statement !== '') {
                DB::unprepared($statement);
                $count++;
            }
        }

        return $count;
    }

    /** @return array<int, array{name:string, size:int, date:\Carbon\Carbon}> */
    public static function list(): array
    {
        return collect(File::files(self::dir()))
            ->filter(fn ($f) => str_ends_with($f->getFilename(), '.sql.gz'))
            ->map(fn ($f) => [
                'name' => $f->getFilename(),
                'size' => $f->getSize(),
                'date' => \Carbon\Carbon::createFromTimestamp($f->getMTime(), config('app.timezone')),
            ])
            ->sortByDesc('date')->values()->all();
    }

    /** Efase backup ki pi ansyen pase $days jou (toujou kenbe dènye a). */
    public static function prune(int $days): int
    {
        $limit = now()->subDays(max(1, $days));
        $deleted = 0;

        foreach (array_slice(self::list(), 1) as $b) {
            if ($b['date']->lt($limit)) {
                File::delete(self::dir() . '/' . $b['name']);
                $deleted++;
            }
        }

        return $deleted;
    }

    /** Chemen sekirize: sèlman yon non fichye nan dosye backups la. */
    public static function path(string $file): string
    {
        $name = basename($file);
        if (!preg_match('/^autogestion-[\d-]+\.sql\.gz$/', $name) || !is_file(self::dir() . '/' . $name)) {
            throw new RuntimeException('Sauvegarde introuvable.');
        }

        return self::dir() . '/' . $name;
    }

    public static function humanSize(int $bytes): string
    {
        return $bytes >= 1048576 ? number_format($bytes / 1048576, 1, ',', ' ') . ' Mo' : max(1, (int) round($bytes / 1024)) . ' Ko';
    }

    /** @return array<string, string> tab → CREATE TABLE */
    private static function tables(string $driver): array
    {
        if ($driver === 'sqlite') {
            return collect(DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"))
                ->mapWithKeys(fn ($t) => [$t->name => $t->sql])->all();
        }

        return collect(DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'"))
            ->map(fn ($t) => array_values((array) $t)[0])
            ->mapWithKeys(fn ($name) => [$name => array_values((array) DB::selectOne("SHOW CREATE TABLE `{$name}`"))[1]])
            ->all();
    }
}
