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
        Schema::table('localizacoes', function (Blueprint $table) {
            $table->string('nome_reduzido', 20)->nullable()->after('nome_localizacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('localizacoes', function (Blueprint $table) {
            $table->dropColumn('nome_reduzido');
        });
    }
};
