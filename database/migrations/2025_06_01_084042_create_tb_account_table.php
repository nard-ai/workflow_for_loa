<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_account', function (Blueprint $table) {
            $table->id('accnt_id');
            $table->string('Emp_No');
            $table->foreign('Emp_No')->references('Emp_No')->on('tb_employeeinfo');
            $table->foreignId('department_id')->constrained('tb_department', 'department_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('position', ['Head', 'Staff', 'Admin',]); // Added Admin here
            $table->enum('accessRole', ['Approver', 'Viewer', 'Admin']); // Added Admin here
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_account');
    }
};
