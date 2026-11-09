<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->string('price', 100);
            $table->string('image')->default('laptop_hp.jpg');
            $table->text('description');
            $table->timestamps();
        });
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('service', 120);
            $table->text('note');
            $table->string('address', 500)->default('Sin recogida a domicilio');
            $table->string('status', 40)->default('Recibido en sistema');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('products');
    }
};
