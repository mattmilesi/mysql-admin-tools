<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TargetDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');

        Schema::connection('target_mysql')
            ->dropIfExists('users');

        Schema::connection('target_mysql')
            ->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('first_name');
                $table->string('last_name');
                $table->timestamps();
            });

        for ($i = 0; $i < 1000; $i++) {
            $batch = [];
            for ($k = 0; $k < 1000; $k++) {
                $batch[] = [
                    'first_name' => Str::random(30),
                    'last_name' => Str::random(40),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::connection('target_mysql')
                ->table('users')
                ->insert($batch);
            unset($batch);
        }
    }
}
