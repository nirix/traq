<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('codename')->nullable();
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->bigInteger('next_ticket_id')->default(1);
            $table->boolean('enable_wiki')->default(false);
            $table->string('default_ticket_sorting')->default('priority.asc');
            $table->integer('display_order')->default(0);
            $table->integer('default_status_id');
            $table->integer('default_priority_id');
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
        Schema::dropIfExists('projects');
    }
}
