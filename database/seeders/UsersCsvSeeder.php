<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Log\Logger;

class UsersCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = now()->format('Y-m-d H:i:s');
        $csvFile = database_path('data\users-1M.csv');
        // $csvFile = database_path('data\users-1K.csv');
        $tasks = [];
        $numberOfProcesses = 10;


        for ($i = 0; $i < $numberOfProcesses; $i++) {
            $tasks[] = function () use ($csvFile, $now, $i, $numberOfProcesses) {
                DB::reconnect();

                $handle = fopen($csvFile, 'r');
                if ($handle === false) {
                    throw new \Exception("Could not open the file!");
                }
                fgets($handle);

                $currentLine = 0;
                $userData = [];
                $addressData = [];

                while (($line = fgets($handle)) !== false) {
                    if ($currentLine++ % $numberOfProcesses !== $i) {
                        
                        continue;
                    }

                    $row = str_getcsv($line);

                    $id = (string) Str::uuid();
                    $userData[] = [
                        'id'  => $id,
                        'first_name' => $row[2],
                        'last_name' => $row[4],
                        'email' => $row[6],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $addressData[] = [
                        'user_id'  => $id,
                        'country' => $row[30],
                        'city' => $row[31],
                        'post_code' => $row[33],
                        'street' => $row[29],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (count($userData) === 1000) {
                        DB::table('users')->insert($userData);
                        DB::table('addresses')->insert($addressData);
                        $userData = [];
                        $addressData = [];
                    }

                }

                if (!empty($userData)) {
                    DB::table('users')->insert($userData);
                    DB::table('addresses')->insert($addressData);
                }

                fclose($handle);
            };
        }

        Concurrency::run($tasks);
    }
}
