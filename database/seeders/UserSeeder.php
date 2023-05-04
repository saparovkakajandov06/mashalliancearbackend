<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Generator;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Generator::class);
        User::create([
           'first_name' => 'Дмитрий',
           'username' => 'yamaha',
           'password' => Hash::make('12345')
        ]);
        User::factory(10)->create();
        $users = User::all();
        foreach ($users as $user) {
            $owner_id = User::whereNot('id', $user->id)->inRandomOrder()->first()->id;
            $user_array = [$owner_id, $user->id];
            $chat = Chat::where(function ($query) use ($owner_id, $user){
                return $query->where('owner_id', $owner_id)->orWhere('owner_id', $user->id);
            })->where(function ($query) use ($owner_id, $user){
                return $query->where('user_id', $owner_id)->orWhere('user_id', $user->id);
            })->count();
            
            if (!$chat) {
                $chat = Chat::create([
                    'user_id' => $owner_id,
                    'owner_id' => $user->id,
                ]);
                for ($i= 0; $i < rand(2,20); $i++){
                    $message = $chat->messages()->create([
                        'user_id' => $user_array[array_rand([$user->id, $owner_id])],
                        'message' => $faker->realText(100)
                    ]);
                }
                
            }
        }
    }
}
