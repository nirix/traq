<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->insert(
            [
                [
                    'name' => 'Hello World',
                    'codename' => 'Greeting',
                    'slug' => 'hello-world',
                    'description' => '',
                    'next_ticket_id' => 1,
                    'default_status_id' => 1,
                    'default_priority_id' => 2,
                ],
                [
                    'name' => 'Test Project',
                    'codename' => 'Test',
                    'slug' => 'test-project',
                    'description' => 'This is a _test_.',
                    'next_ticket_id' => 1,
                    'default_status_id' => 1,
                    'default_priority_id' => 2,
                ],
            ]
        );
    }
}
