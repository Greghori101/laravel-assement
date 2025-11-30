<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SeedUsers extends Command
{
    protected $signature = 'seed:users {--process=0} {--total=1} {--batch=1000}';

    protected $description = 'Seed users and addresses in parallel with proper ranges';

    public function handle()
    {
        $allUsers = 1_000_000;
        $totalProcesses = (int)$this->option('total');
        $batch = (int)$this->option('batch');

        $usersPerProcess = (int) $allUsers / $totalProcesses;
        $batches = (int) $usersPerProcess / $batch;

        for ($i = 0; $i < $batches; $i++) {
            $users = [];
            $addresses = [];

            // Generate Batch in Memory
            for ($j = 0; $j < $batch; $j++) {
                $user = User::factory()
                    ->make()
                    ->toArray();

                $users[] = $user;
                $addresses[] = Address::factory()
                    ->make(['user_id' => $user['id']])
                    ->toArray();
            }


            // Bulk Insert
            DB::transaction(function () use ($users, $addresses) {
                foreach (array_chunk($users, 1000) as $chunk) {
                    DB::table('users')->insert($chunk);
                }
                foreach (array_chunk($addresses, 1000) as $chunk) {
                    DB::table('addresses')->insert($chunk);
                }
            });
        }
    }
}
