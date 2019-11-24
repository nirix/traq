<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Traq\Type;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('bullet')->default('*');
            $table->boolean('show_on_changelog')->default(true);
            $table->longText('template')->nullable();
            $table->timestamps();
        });

        DB::table('types')->insert([
            [
                'name' => 'Bug',
                'bullet' => '-',
            ],
            [
                'name' => 'Feature',
                'bullet' => '+',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types');
    }
}
