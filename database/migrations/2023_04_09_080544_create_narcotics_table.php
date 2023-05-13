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
        Schema::create('narcotics', function (Blueprint $table) {
            $table->id();
            $table->string('narcotic_category');
            $table->string('narcotic_type');
            $table->string('narcotic_location');
            $table->string('narcotic_district');
            $table->string('narcotic_village');
            $table->string('narcotic_name')->nullable();
            $table->string('narcotic_age')->nullable();
            $table->string('narcotic_sex')->nullable();
            $table->string('narcotic_isGovernment')->nullable();
            $table->string('narcotic_height')->nullable();
            $table->string('narcotic_weight')->nullable();
            $table->string('narcotic_style')->nullable();
            $table->string('narcotic_occupation')->nullable();
            $table->string('narcotic_company')->nullable();
            $table->string('narcotic_address')->nullable();
            $table->string('narcotic_is_local_people')->nullable();
            $table->string('narcotic_details')->nullable();
            $table->string('narcotic_car_type')->nullable();
            $table->string('narcotic_car_brand')->nullable();
            $table->string('narcotic_car_color')->nullable();
            $table->string('narcotic_car_registration')->nullable();
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
        Schema::dropIfExists('narcotics');
    }
};
