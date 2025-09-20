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
        Schema::table('users', function (Blueprint $table) {
            // Rename existing columns to match our domain model
            $table->renameColumn('name', 'nombre');
            
            // Add new columns
            $table->string('apellido_paterno')->nullable()->after('nombre');
            $table->string('apellido_materno')->nullable()->after('apellido_paterno');
            $table->boolean('activo')->default(true)->after('password');
            
            // Remove columns we don't need
            $table->dropColumn(['email_verified_at', 'remember_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('nombre', 'name');
            $table->dropColumn(['apellido_paterno', 'apellido_materno', 'activo']);
            
            // Add back removed columns
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });
    }
};
