<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->integer('id', true); 
            $table->string('nombre', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('telefono', 20);
            $table->enum('tipoUsuario', ['admin', 'cliente'])->default('cliente');
            $table->integer('puntosRacha')->default(0);
            $table->tinyInteger('descuentoActivo')->default(0);
            $table->timestamps();
            $table->rememberToken();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};