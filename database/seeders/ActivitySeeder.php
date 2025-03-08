<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $uniquePoints = range(1, 1000); // Generate unique points from 1 to 1000
        shuffle($uniquePoints); // Shuffle to randomize

        for ($i = 0; $i < 1000; $i++) {
            $user = $users->random(); // Pick a random user

            Activity::create([
                'user_id' => $user->id,
                'points' => $uniquePoints[$i], // Assign unique points
                'performed_at' => Carbon::now()->subDays(rand(1, 30))
            ]);
        }
    }


}
