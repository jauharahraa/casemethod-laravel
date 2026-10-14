<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('summary', 300)->nullable();
            $t->longText('content');
            $t->string('theme', 20)->default('blue');
            $t->string('icon', 30)->default('book-open');
            $t->string('link_url', 500)->nullable();
            $t->string('link_label', 60)->nullable();
            $t->string('status', 20)->default('draft'); // draft | published
            $t->timestamps();
        });

        Schema::create('material_reads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('material_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['material_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_reads');
        Schema::dropIfExists('materials');
    }
};