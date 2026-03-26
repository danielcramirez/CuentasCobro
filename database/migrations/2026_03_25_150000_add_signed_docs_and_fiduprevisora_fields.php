<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cuentas_cobro', function (Blueprint $table) {
            $table->string('documento_1_firmado_path')->nullable()->after('planilla_pdf_path');
            $table->string('documento_2_firmado_path')->nullable()->after('documento_1_firmado_path');
            $table->string('returned_stage')->nullable()->after('returned_at');
        });

        Schema::table('cuenta_cobro_documentos', function (Blueprint $table) {
            $table->enum('fiduprevisora_estado', ['pendiente', 'aprobado', 'rechazado'])
                ->default('pendiente')
                ->after('comentario_supervisor');
            $table->text('fiduprevisora_comentario')->nullable()->after('fiduprevisora_estado');
            $table->timestamp('fiduprevisora_validado_at')->nullable()->after('fiduprevisora_comentario');
        });

        DB::statement("ALTER TABLE cuentas_cobro MODIFY fiduprevisora_status ENUM('pendiente', 'en_revision', 'en_tramite', 'pagado', 'rechazada') NOT NULL DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cuentas_cobro MODIFY fiduprevisora_status ENUM('pendiente', 'en_tramite', 'pagado') NOT NULL DEFAULT 'pendiente'");

        Schema::table('cuenta_cobro_documentos', function (Blueprint $table) {
            $table->dropColumn([
                'fiduprevisora_estado',
                'fiduprevisora_comentario',
                'fiduprevisora_validado_at',
            ]);
        });

        Schema::table('cuentas_cobro', function (Blueprint $table) {
            $table->dropColumn([
                'documento_1_firmado_path',
                'documento_2_firmado_path',
                'returned_stage',
            ]);
        });
    }
};
