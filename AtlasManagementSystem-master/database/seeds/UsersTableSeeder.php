<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Users\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['over_name' => '藤瀬','under_name' => '悠也','over_name_kana' => 'フジセ','under_name_kana' => 'ユウヤ','mail_address' => 'hizen0625@gmail.com','sex' => '1','role' => '1','birth_day' => '1998-06-25','password' => bcrypt('password'),'created_at' => date('Y-m-d H:i:s')]
        ]);

    }
}