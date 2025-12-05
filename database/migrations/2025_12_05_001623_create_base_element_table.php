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
        Schema::create('base_element', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_id')->constrained()->onDelete('cascade');
            $table->foreignId('element_id')->constrained()->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->timestamps();

            // Assurer l'unicité de la combinaison base_id et element_id
            $table->unique(['base_id', 'element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('base_element');
    }
};

