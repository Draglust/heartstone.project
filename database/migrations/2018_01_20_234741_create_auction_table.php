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
            $table->increments('id');
            $table->integer('auc',10);
            $table->integer('item',10);
            $table->string('owner',20);
            $table->string('ownerRealm',20);
            $table->integer('bid',15);
            $table->integer('buyout',15);
            $table->integer('quantity',5);
            $table->string('timeLeft',15);
            $table->integer('rand',5);
            $table->integer('seed',15);
            $table->integer('context',5);
            $table->integer('petSpeciesId',5);
            $table->integer('petBreedId',5);
            $table->integer('petLevel',5);
            $table->integer('petQualityId',5);
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
