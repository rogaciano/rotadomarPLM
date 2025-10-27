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
        Schema::table('tecido_cor_estoques', function (Blueprint $table) {
            $table->string('cor_rgb', 7)->nullable(); // Formato #RRGGBB
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tecido_cor_estoques', function (Blueprint $table) {
            $table->dropColumn('cor_rgb');
        });
    }
};
