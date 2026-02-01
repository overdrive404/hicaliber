<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->unsignedTinyInteger('bedrooms');
            $table->unsignedTinyInteger('bathrooms');
            $table->unsignedTinyInteger('storeys');
            $table->unsignedTinyInteger('garages');
            $table->timestamps();

            $table->index('name');
            $table->index('price');
            $table->index('bedrooms');
            $table->index('bathrooms');
            $table->index('storeys');
            $table->index('garages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
