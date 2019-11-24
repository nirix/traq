<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priorities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('display_order');
            $table->timestamps();
        });

        $priorities = [
            'Low' => 1,
            'Normal' => 5,
            'High' => 10,
        ];

        foreach ($priorities as $priority => $displayLevel) {
            DB::table('priorities')->insert([
                'name' => $priority,
                'display_order' => $displayLevel,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('priorities');
    }
}
