<?php

use Illuminate\Database\Seeder;

class MilestonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('milestones')->insert(
            [
                [
                    'name' => 'v0.1',
                    'codename' => '',
                    'slug' => 'v0.1',
                    'description' => 'First version',
                    'project_id' => 2,
                    'created_at' => now(),
                ],
                [
                    'name' => 'v0.2',
                    'codename' => '',
                    'slug' => 'v0.2',
                    'description' => '',
                    'project_id' => 2,
                    'created_at' => now(),
                ],
                [
                    'name' => 'v0.3',
                    'codename' => '',
                    'slug' => 'v0.3',
                    'description' => 'This is a test.',
                    'project_id' => 2,
                    'created_at' => now(),
                ],
            ]
        );
    }
}
