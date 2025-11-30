<?php

use App\Enums\RoleEnum;
use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('position');

            $table->foreignIdFor(Employee::class, 'manager_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('role', RoleEnum::getValues());

            $table->integer('leave_balance')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
