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
        Schema::create('corruptions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_line_id');
            $table->string('lineUUID');
            $table->string('corruption_name');
            $table->string('corruption_position');
            $table->string('corruption_affiliation');
            $table->string('corruption_details');
            $table->string('corruption_status');
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
        Schema::dropIfExists('corruptions');
    }
};
