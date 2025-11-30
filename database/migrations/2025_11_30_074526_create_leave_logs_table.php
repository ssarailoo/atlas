<?php

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LeaveRequest::class, 'leave_request_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('action');
            $table->foreignIdFor(Employee::class, 'performed_by')
                ->constrained()
                ->cascadeOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_logs');
    }
};
