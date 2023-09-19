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
        Schema::create('components_crm', function (Blueprint $table) {
            $table->id();
            $table->string('component_name');
            $table->string('path')->nullable();
            $table->string('type');
            $table->string('category');
            $table->boolean('status');
            $table->string('preview')->nullable();
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
        Schema::dropIfExists('components_crm');
    }
};
