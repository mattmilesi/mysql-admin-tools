<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsageController extends Controller
{
    public function show()
    {
        Config::set('database.connections.target_mysql_info', [
            'driver' => 'mysql',
            'host' => env('TARGET_DB_HOST'),
            'port' => env('TARGET_DB_PORT'),
            'database' => 'information_schema',
            'username' => env('TARGET_DB_USERNAME'),
            'password' => env('TARGET_DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]);

        DB::connection('target_mysql_info')->statement('SET information_schema_stats_expiry = 0;');

        $autoincTables = DB::connection('target_mysql_info')
            ->table('TABLES AS t')
            ->join('COLUMNS AS c', function ($join) {
                $join->on('t.TABLE_SCHEMA', '=', 'c.TABLE_SCHEMA')
                    ->on('t.TABLE_NAME', '=', 'c.TABLE_NAME');
            })
            ->where('t.TABLE_SCHEMA', env('TARGET_DB_DATABASE'))
            ->where('c.COLUMN_KEY', 'PRI')
            ->whereNotNull('t.AUTO_INCREMENT')
            ->select('t.TABLE_NAME', 't.AUTO_INCREMENT', 'c.COLUMN_NAME', 'c.COLUMN_TYPE')
            ->get();

        $usages = [];
        foreach ($autoincTables as $table) {
            $maxSize = $this->getMaxSize($table->COLUMN_TYPE);
            $info = [
                'auto_increment' => $table->AUTO_INCREMENT,
                'pk' => $table->COLUMN_NAME,
                'pk_type' => $table->COLUMN_TYPE,
                'pk_max_size' => $maxSize,
            ];
            if ($maxSize) {
                $info['usage'] = round($table->AUTO_INCREMENT / $maxSize * 100, 2);
            }
            $usages[$table->TABLE_NAME] = $info;
        }

        uksort($usages, fn($a, $b) => $usages[$b]['usage'] <=> $usages[$a]['usage']);

        //return $usages;

        return view('usage.usage-index', ['usages' => $usages]);
    }

    private function getMaxSize(string $type): ?float
    {
        return match ($type) {
            'tinyint' => pow(2, 7) - 1,
            'tinyint unsigned' => pow(2, 8) - 1,
            'smallint' => pow(2, 15) - 1,
            'smallint unsigned' => pow(2, 16) - 1,
            'mediumint' => pow(2, 23) - 1,
            'mediumint unsigned' => pow(2, 24) - 1,
            'int' => pow(2, 31) - 1,
            'int unsigned' => pow(2, 32) - 1,
            'bigint' => pow(2, 63) - 1,
            'bigint unsigned' => pow(2, 64) - 1,
            default => null,
        };
    }
}
