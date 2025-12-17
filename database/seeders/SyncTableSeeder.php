<?php

namespace DataPlay\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sync')->insert([
            [
                'key_hash' => 'KEY_HASH',
                'data_hash' => 'DATA_HASH',
                'status' => 'STATUS',
                'is_active' => true,
                'data' => '{}',
            ],
        ]);
    }
}
