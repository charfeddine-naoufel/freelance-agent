<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('freelancer_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->string('url')->nullable();

            $table->string('repository_url')->nullable();

            $table->json('technologies')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};