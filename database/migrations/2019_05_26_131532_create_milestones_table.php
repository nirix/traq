<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Traq\Milestone;

class CreateMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('codename')->nullable();
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->smallInteger('status')->default(Milestone::STATUS_ACTIVE);
            $table->date('due_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->bigInteger('project_id');
            $table->integer('display_order')->default(0);
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
        Schema::dropIfExists('milestones');
    }
}
