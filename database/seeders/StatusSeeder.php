<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::insert([
            ['name' => 'Default'],
            ['name' => 'Transferred'],
            ['name' => 'Not Transferred'],
            ['name' => 'Approved'],
            ['name' => 'Shipped'],
            ['name' => 'Billable'],
            ['name' => 'On Chase'],
            ['name' => 'Paid']
        ]);
    }
}
