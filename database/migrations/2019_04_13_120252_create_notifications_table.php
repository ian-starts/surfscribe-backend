<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique()->index();
            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['location_id', 'user_id']);
            $table->string('wind_direction');
            $table->string('wind_direction_exact_match');
            $table->string('wind_speed_unit');
            $table->float('wind_speed_min');
            $table->float('wind_speed_max');
            $table->string('swell_height_unit');
            $table->float('swell_height_min');
            $table->float('swell_height_max');
            $table->float('swell_period_min');
            $table->float('swell_period_max');
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
        Schema::dropIfExists('notifications');
    }
}
