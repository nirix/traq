<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Traq\Permissions;

class CreateProjectRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('project_id')->nullable();
            $table->json('permissions');
            $table->timestamps();
        });

        DB::table('project_roles')->insert([
            [
                'name' => 'Project Manager',
                'project_id' => null,
                'permissions' => json_encode([
                    Permissions::PERMISSION_MILESTONE_CREATE,
                    Permissions::PERMISSION_MILESTONE_UPDATE,
                    Permissions::PERMISSION_MILESTONE_DELETE,
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
        Schema::dropIfExists('project_roles');
    }
}
