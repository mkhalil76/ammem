<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
/*
        $org = new \App\Organization();
        $org->name = 'تكنولوجيا المعلومات';
        $org->save();

        $org = new \App\Activity();
        $org->name = 'نشاط';
        $org->save();

        $job = new \App\Job();
        $job->name = 'مهندس حاسوب';
        $job->save();

        */
        $user = new \App\User();

        $user->name = 'محمد بن سلمان';
        $user->region = 'الشرق الاوسط';
        $user->country = 'السعوديه';
        $user->activity_id = 1;
        $user->organization_id = 1;
        $user->mobile = '0591111111';
        $user->gender = 'male';
        $user->job_id = 1;
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('123456');
        $user->save();
    }
}
