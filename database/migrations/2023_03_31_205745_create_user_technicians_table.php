<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_technicians', function (Blueprint $table) {
            $table->id();
            $table->string('lineUUID');
            $table->string('displayName');
            $table->string('pictureUrl');
            $table->string('name');
            $table->string('tel');
            $table->string('citizen_id');
            $table->string('email')->nullable();
            $table->string('level');
            $table->string('address');
            $table->string('status');
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
        Schema::dropIfExists('user_technicians');
    }
};
