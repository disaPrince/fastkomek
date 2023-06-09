<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_reactions', function (Blueprint $table) {
            $table->id();
            $table->string('reaction');
            $table->integer('telegramId');
            $table->unsignedBigInteger('news_id')->index();
            $table->foreign('news_id')
                  ->references('id')
                  ->on('news')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('news_reactions');
    }
}
