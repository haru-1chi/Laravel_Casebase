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
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->integer('education');
            $table->string('status_');
            $table->integer('dis_type');
            $table->integer('tool');
            $table->string('keeper');
            $table->integer('invest');
            $table->integer('loan');
            $table->string('hobby');
            $table->string('aptitude');
            $table->string('commute');
            $table->string('occupation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};
