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
        Schema::create('cuenta_cobro_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_cobro_id')->constrained('cuentas_cobro')->cascadeOnDelete();
            
            // Número del documento (1-16)
            $table->integer('numero_documento');
            
            // Nombre del documento
            $table->string('nombre_documento');
            
            // Ruta del archivo
            $table->string('archivo_path');
            
            // Estado del documento
            $table->enum('estado', ['pendiente', 'cargado', 'validado', 'rechazado'])->default('pendiente');
            
            // Comentarios del supervisor
            $table->text('comentario_supervisor')->nullable();
            
            // Auditoría
            $table->timestamp('cargado_at')->nullable();
            $table->timestamp('validado_at')->nullable();
            $table->timestamps();
            
            $table->unique(['cuenta_cobro_id', 'numero_documento']);
            $table->index(['cuenta_cobro_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobro_documentos');
    }
};
