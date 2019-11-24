<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ticket_id');
            $table->text('summary');
            $table->longText('description');
            $table->bigInteger('user_id');
            $table->bigInteger('project_id');
            $table->bigInteger('milestone_id');
            $table->bigInteger('version_id')->nullable();
            $table->integer('component_id')->nullable();
            $table->integer('type_id');
            $table->integer('status_id');
            $table->integer('priority_id');
            // $table->integer('severity_id');
            $table->integer('assignee_id')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
