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
        Schema::create('detalles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('pedido_id')->
                constrained()->
                onUpdate('cascade')->
                onDelete('restrict'); //Pedido FK
            $table->foreignId('producto_id')->constrained()->
                onUpdate('cascade')->
                onDelete('restrict');//Producto FK
            $table->integer('cantidad')->nullable(false)->default(1);
            $table->float('precio')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles');
    }
};
