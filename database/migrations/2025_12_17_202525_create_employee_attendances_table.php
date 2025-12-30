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
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date');
            $table->enum('status', ['Present', 'Absent', 'Leave', 'Off'])->nullable();
            $table->string('leave_type')->nullable();
            $table->text('note')->nullable();
            $table->datetime('check_in_time')->nullable();
            $table->datetime('check_out_time')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['POSID', 'employee_id', 'attendance_date'], 'unique_employee_attendance');
            $table->index('POSID', 'idx_employee_attendances_posid');
            $table->index('attendance_date', 'idx_employee_attendances_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
