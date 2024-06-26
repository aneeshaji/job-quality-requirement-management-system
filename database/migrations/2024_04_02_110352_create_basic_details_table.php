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
        Schema::create('basic_details', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->nullable();
            $table->string('job_name')->nullable();
            $table->string('stages')->nullable();
            $table->date('release_date')->nullable();
            $table->date('due_date')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 => Active, 0 => Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basic_details');
    }
};
