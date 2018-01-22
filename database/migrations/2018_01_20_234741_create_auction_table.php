<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuctionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('auc');
            $table->integer('item');
            $table->string('owner',20);
            $table->string('ownerRealm',20);
            $table->integer('bid');
            $table->integer('buyout');
            $table->integer('quantity');
            $table->string('timeLeft',15);
            $table->integer('rand');
            $table->integer('seed');
            $table->integer('context');
            $table->integer('petSpeciesId');
            $table->integer('petBreedId');
            $table->integer('petLevel');
            $table->integer('petQualityId');
            $table->rememberToken();
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
        Schema::drop('auctions');
    }
}
