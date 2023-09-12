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
        Schema::create('agency_websites', function (Blueprint $table) {
            $table->id();
            $table->integer('website_category_id')->constrained();
            $table->string('business_name');
            $table->string('address');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->integer('agency_id')->constrained();
            $table->string('status')->default('active');
            $table->integer('website_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('agency_websites');
    }
};
