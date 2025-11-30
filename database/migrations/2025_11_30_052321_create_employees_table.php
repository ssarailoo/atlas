<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('position');

            $table->foreignId('manager_id')
            ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->enum('role', ['employee', 'manager', 'hr', 'ceo']);

            $table->integer('leave_balance')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
