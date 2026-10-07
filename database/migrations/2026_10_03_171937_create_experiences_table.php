<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('freelancer_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('company')->nullable();

            $table->string('position');

            $table->text('description')->nullable();

            $table->date('started_at')->nullable();

            $table->date('ended_at')->nullable();

            $table->boolean('is_current')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};