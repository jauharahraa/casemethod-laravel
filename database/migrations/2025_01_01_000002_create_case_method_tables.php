<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->text('description')->nullable();
            $t->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('code', 20)->unique();
            $t->timestamps();
        });

        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('full_name');
            $t->string('participant_number', 50)->unique();
            $t->timestamps();
        });

        Schema::create('classroom_student', function (Blueprint $t) {
            $t->id();
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->unique(['classroom_id', 'student_id']);
        });

        Schema::create('cases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->longText('description');
            $t->text('instructions');
            $t->text('objectives');
            $t->dateTime('deadline')->nullable();
            $t->string('status', 20)->default('draft');
            $t->timestamps();
        });

        Schema::create('rubric_versions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $t->unsignedInteger('version');
            $t->json('data');
            $t->timestamps();
            $t->unique(['case_id', 'version']);
        });

       Schema::create('submissions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('rubric_version_id')->constrained('rubric_versions')->cascadeOnDelete();
            $t->string('status', 20)->default('draft'); // draft, feedback, revising, locked
            $t->dateTime('submitted_at')->nullable();
            $t->dateTime('locked_at')->nullable();
            $t->timestamps();
            $t->unique(['case_id', 'student_id']);
        });

        Schema::create('submission_versions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('version'); // 1 = jawaban pertama, 2 = revisi
            $t->longText('identification')->nullable();
            $t->longText('analysis')->nullable();
            $t->longText('solution')->nullable();
            $t->string('status', 20)->default('draft'); // draft, submitted
            $t->dateTime('submitted_at')->nullable();
            $t->timestamps();
            $t->unique(['submission_id', 'version']); // batas revisi di level database
        });

        Schema::create('ai_feedbacks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('submission_version_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('mode', 20); // gemini | simulasi
            $t->string('status', 20); // ok | failed
            $t->json('content')->nullable();
            $t->text('error')->nullable();
            $t->timestamps();
        });

        Schema::create('grades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('submission_id')->unique()->constrained()->cascadeOnDelete();
            $t->json('criterion_scores');
            $t->json('aspect_scores');
            $t->decimal('final_score', 5, 2);
            $t->text('comment')->nullable();
            $t->boolean('published')->default(false);
            $t->dateTime('published_at')->nullable();
            $t->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('owner_id')->nullable(); // guru pemilik data, untuk tampilan aktivitas
            $t->string('action', 50);
            $t->string('description');
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'grades', 'ai_feedbacks', 'submission_versions', 'submissions', 'rubric_versions', 'cases', 'classroom_student', 'students', 'classrooms', 'subjects'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
