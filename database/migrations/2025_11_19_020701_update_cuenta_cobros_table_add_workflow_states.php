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
        Schema::table('cuenta_cobros', function (Blueprint $table) {
            // Agregar columnas para el flujo de aprobación
            $table->timestamp('aprobado_supervisor_at')->nullable()->after('estado');
            $table->foreignId('aprobado_supervisor_by')->nullable()->constrained('users')->after('aprobado_supervisor_at');
            
            $table->timestamp('aprobado_contratacion_at')->nullable()->after('aprobado_supervisor_by');
            $table->foreignId('aprobado_contratacion_by')->nullable()->constrained('users')->after('aprobado_contratacion_at');
            
            $table->timestamp('aprobado_tesoreria_at')->nullable()->after('aprobado_contratacion_by');
            $table->foreignId('aprobado_tesoreria_by')->nullable()->constrained('users')->after('aprobado_tesoreria_at');
            
            $table->timestamp('aprobado_ordenador_at')->nullable()->after('aprobado_tesoreria_by');
            $table->foreignId('aprobado_ordenador_by')->nullable()->constrained('users')->after('aprobado_ordenador_at');
            
            $table->text('comentarios_rechazo')->nullable()->after('aprobado_ordenador_by');
            $table->foreignId('rechazado_by')->nullable()->constrained('users')->after('comentarios_rechazo');
            $table->timestamp('rechazado_at')->nullable()->after('rechazado_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuenta_cobros', function (Blueprint $table) {
            $table->dropForeign(['aprobado_supervisor_by']);
            $table->dropForeign(['aprobado_contratacion_by']);
            $table->dropForeign(['aprobado_tesoreria_by']);
            $table->dropForeign(['aprobado_ordenador_by']);
            $table->dropForeign(['rechazado_by']);
            
            $table->dropColumn([
                'aprobado_supervisor_at',
                'aprobado_supervisor_by',
                'aprobado_contratacion_at',
                'aprobado_contratacion_by',
                'aprobado_tesoreria_at',
                'aprobado_tesoreria_by',
                'aprobado_ordenador_at',
                'aprobado_ordenador_by',
                'comentarios_rechazo',
                'rechazado_by',
                'rechazado_at'
            ]);
        });
    }
};
