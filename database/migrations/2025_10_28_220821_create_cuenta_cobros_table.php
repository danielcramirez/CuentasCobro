<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_cobros', function (Blueprint $table) {
            $table->id()->autoIncrement()->primary();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->date('fecha_emision')->nullable(false);
            $table->string('proyecto_servicio', 255)->nullable(false);
            $table->decimal('valor', 10, 2)->nullable(false);
            $table->string('estado', 100)->nullable(false)->default('pendiente');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->nullable(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobros');
    }
};
