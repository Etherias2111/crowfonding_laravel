<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('person_type', 20)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('first_surname', 100)->nullable();
            $table->string('second_surname', 100)->nullable();
            $table->string('document_number', 50)->nullable()->unique();
            $table->string('tax_id', 20)->nullable()->unique();
            $table->string('legal_name', 180)->nullable();
            $table->string('representative_name', 180)->nullable();
            $table->string('commercial_name', 180)->nullable();
            $table->string('representative_document_number', 50)->nullable();
            $table->string('phone_secondary', 16)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('city', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['document_number']);
            $table->dropUnique(['tax_id']);
            $table->dropColumn([
                'person_type', 'first_name', 'middle_name', 'first_surname', 'second_surname',
                'document_number', 'tax_id', 'legal_name', 'representative_name', 'commercial_name',
                'representative_document_number', 'phone_secondary', 'address', 'department', 'city',
            ]);
        });
    }
};
