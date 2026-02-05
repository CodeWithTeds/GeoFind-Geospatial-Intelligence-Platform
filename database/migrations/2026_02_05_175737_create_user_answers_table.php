<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->decimal('answer_latitude', 9, 6)->nullable(); // User's submitted latitude
            $table->decimal('answer_longitude', 9, 6)->nullable(); // User's submitted longitude
            $table->integer('stars')->nullable(); // Star rating 1, 2, or 3
            $table->boolean('is_correct'); // Whether the answer is correct
            $table->timestamp('answered_at')->useCurrent();
        });

        // Add check constraint for stars
        // Note: Check constraints might not be supported in all DB versions (e.g. old MySQL), but widely supported in modern MariaDB/MySQL/Postgres.
        // XAMPP usually comes with MariaDB which supports CHECK constraints.
        try {
            DB::statement('ALTER TABLE user_answers ADD CONSTRAINT check_stars_range CHECK (stars BETWEEN 1 AND 3)');
        } catch (\Exception $e) {
            // If check constraint fails (e.g. unsupported), we can log it or ignore it, 
            // but for now we assume it works or the user accepts application-level validation.
            // Leaving it to throw might be better to be explicit.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
