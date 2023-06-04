<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultOfReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_of_reactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('good');
            $table->string('bad');
            $table->string('whatever');
            $table->unsignedBigInteger('news_reactions_id')->index();
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
        Schema::dropIfExists('result_of_reactions');
    }
}
