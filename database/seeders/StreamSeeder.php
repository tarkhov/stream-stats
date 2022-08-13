<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\StreamService;

class StreamSeeder extends Seeder
{
    const PER_PAGE = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        StreamService::seed();
    }
}
