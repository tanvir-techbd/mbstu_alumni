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
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Personal
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();

            // Academic
            $table->string('student_id')->nullable()->index();
            $table->string('department')->nullable();
            $table->string('program')->nullable();
            $table->string('batch')->nullable();
            $table->string('session')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();

            // Professional
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('industry')->nullable();
            $table->unsignedTinyInteger('years_of_experience')->nullable();
            $table->string('country')->nullable();
            $table->string('district')->nullable();
            $table->text('office_address')->nullable();

            // Social links
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('portfolio_url')->nullable();

            // Additional
            $table->text('skills')->nullable();
            $table->text('biography')->nullable();
            $table->text('interests')->nullable();

            // Verification
            $table->string('verification_status')->default('pending')->index();
            $table->string('verification_document_path')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
