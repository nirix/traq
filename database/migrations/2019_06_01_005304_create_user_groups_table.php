<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Traq\Permissions;

class CreateUserGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        DB::table('user_groups')->insert([
            [
                'name' => 'Admin',
                'permissions' => json_encode([
                    Permissions::PERMISSION_ADMIN,
                ]),
            ],
            [
                'name' => 'Registered',
                'permissions' => json_encode([
                    Permissions::PERMISSION_TICKET_CREATE,
                    Permissions::PERMISSION_TICKET_UPDATE,
                    Permissions::PERMISSION_WIKI_VIEW,
                ]),
            ],
            [
                'name' => 'Guest',
                'permissions' => json_encode([

                ]),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_groups');
    }
}
