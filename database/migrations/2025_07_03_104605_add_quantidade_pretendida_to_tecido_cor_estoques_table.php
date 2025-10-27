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
            $table->decimal('quantidade_pretendida', 10, 2)->default(0)->comment('Quantidade pretendida para esta cor');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tecido_cor_estoques', function (Blueprint $table) {
            $table->dropColumn('quantidade_pretendida');
        });
    }
};
