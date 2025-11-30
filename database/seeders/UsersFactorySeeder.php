<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;

class UsersFactorySeeder extends Seeder
{

    use WithoutModelEvents;

    public function run()
    {
        $totalProcesses = 10;
        echo "Spawning $totalProcesses parallel processes...\n";

        Process::pool(function ($pool) use ($totalProcesses) {
            for ($i = 0; $i < $totalProcesses; $i++) {
                $pool->command(
                    "php artisan seed:users --process=$i --total=$totalProcesses"
                )->timeout(3600);
            }
        })->start()->wait();

        $users  = User::count();
        echo "All processes finished.\ntotal users seeded: $users\n";
    }
}
