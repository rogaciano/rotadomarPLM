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
        Schema::table('produto_observacao', function (Blueprint $table) {
            // Alterar deleted_at para aceitar NULL
            $table->timestamp('deleted_at')->nullable()->change();
        });
        
        // Corrigir registros com deleted_at zerado
        DB::statement("UPDATE produto_observacao SET deleted_at = NULL WHERE deleted_at = '0000-00-00 00:00:00'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto_observacao', function (Blueprint $table) {
            //
        });
    }
};
