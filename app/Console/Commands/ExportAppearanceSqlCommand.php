<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportAppearanceSqlCommand extends Command
{
    protected $signature = 'site:export-appearance-sql
                            {--path=database/dumps/fugefa-megjelenes.sql : Cél SQL fájl}';

    protected $description = 'A publikus megjelenéshez kellő táblák MySQL dumpja (webes importhoz)';

    /**
     * @var list<string>
     */
    private const TABLES = [
        'site_settings',
        'site_pages',
        'fmm_menu_locations',
        'fmm_menus',
        'fmm_menu_items',
    ];

    /**
     * @var list<string>
     */
    private const URL_COLUMNS = [
        'url',
        'html',
        'css',
        'header_html',
        'header_css',
        'header_grapes_data',
        'footer_html',
        'footer_css',
        'footer_grapes_data',
        'grapes_data',
        'custom_css',
        'seo',
    ];

    public function handle(): int
    {
        $relative = str_replace('\\', '/', (string) $this->option('path'));
        $path = base_path($relative);
        File::ensureDirectoryExists(dirname($path));

        $sql = $this->fileHeader();
        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach (array_reverse(self::TABLES) as $table) {
            $sql .= "DELETE FROM `{$table}`;\n";
        }

        $sql .= "\n";

        foreach (self::TABLES as $table) {
            $sql .= $this->dumpTable($table);
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($path, $sql);

        $this->info('SQL kész: '.$relative);
        $this->line('Import a weben: phpMyAdmin vagy `mysql -u USER -p ADATBAZIS < '.$relative.'`');
        $this->line('Utána a szerveren: php artisan site:write-public-css');

        return self::SUCCESS;
    }

    protected function fileHeader(): string
    {
        $exportedAt = now()->toDateTimeString();

        return <<<SQL
-- Fügefa építésziroda – publikus megjelenés (oldalak, téma, fejléc/lábléc, menü)
-- Exportálva: {$exportedAt}
--
-- Előfeltétel: a Laravel migrációk már lefutottak a céladatbázison.
-- Nem tartalmaz felhasználót, jelszót, sessiont.
-- A menü- és oldallinkek relatívak, bármely domainen működnek.
--
-- Import:
--   mysql -u USER -p ADATBAZIS < database/dumps/fugefa-megjelenes.sql
-- Utána a szerveren (a CSS fájlok SQL-ből nem íródnak ki):
--   php artisan site:write-public-css
-- Képek: másold fel a public/images/site mappát is.

SQL;
    }

    protected function dumpTable(string $table): string
    {
        $rows = DB::table($table)->orderBy('id')->get();

        if ($rows->isEmpty()) {
            return "-- `{$table}`: üres\n\n";
        }

        $sql = "-- `{$table}` (".$rows->count()." sor)\n";
        $maxId = 0;

        foreach ($rows as $row) {
            $data = (array) $row;
            $maxId = max($maxId, (int) ($data['id'] ?? 0));
            $data = $this->makePortable($data);

            $columns = implode(', ', array_map(
                static fn (string $column): string => '`'.$column.'`',
                array_keys($data),
            ));
            $values = implode(', ', array_map(fn (mixed $value): string => $this->quote($value), $data));
            $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n";
        }

        if ($maxId > 0) {
            $sql .= "ALTER TABLE `{$table}` AUTO_INCREMENT=".($maxId + 1).";\n";
        }

        return $sql."\n";
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function makePortable(array $data): array
    {
        foreach (self::URL_COLUMNS as $column) {
            if (! array_key_exists($column, $data) || ! is_string($data[$column])) {
                continue;
            }

            $data[$column] = $this->stripLocalOrigin($data[$column], $column === 'url');
        }

        return $data;
    }

    protected function stripLocalOrigin(string $value, bool $emptyBecomesHome): string
    {
        $origins = array_values(array_unique(array_filter([
            rtrim((string) config('app.url'), '/'),
            'https://fugefa-epitesziroda.test',
            'http://fugefa-epitesziroda.test',
        ])));

        usort($origins, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($origins as $origin) {
            if ($origin === '') {
                continue;
            }

            if ($value === $origin || $value === $origin.'/') {
                return '/';
            }

            $value = str_replace($origin, '', $value);
        }

        if ($emptyBecomesHome && $value === '') {
            return '/';
        }

        return $value;
    }

    protected function quote(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::connection()->getPdo()->quote((string) $value);
    }
}
