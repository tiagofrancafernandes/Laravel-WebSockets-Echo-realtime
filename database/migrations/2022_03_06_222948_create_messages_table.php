<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->longText('message');
            $table->boolean('readed')->nullable()->default(false);
            $table->unsignedBigInteger('from');
            $table->foreign('from')->references('id')
            ->on('users')->onDelete('cascade');//cascade|set null

            $table->unsignedBigInteger('to');
            $table->foreign('to')->references('id')
            ->on('users')->onDelete('cascade');//cascade|set null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
