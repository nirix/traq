<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Traq\Status;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->smallInteger('status')->default(Status::STATUS_NEW);
            $table->boolean('show_on_changelog')->default(true);
            $table->timestamps();
        });

        DB::table('statuses')->insert(
            [
                [
                    'name' => 'New',
                    'status' => Status::STATUS_NEW,
                ],
                [
                    'name' => 'Started',
                    'status' => Status::STATUS_STARTED,
                ],
                [
                    'name' => 'Closed',
                    'status' => Status::STATUS_CLOSED,
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
