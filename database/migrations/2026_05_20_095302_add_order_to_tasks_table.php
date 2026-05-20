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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('todo_id');
        });

        DB::statement('UPDATE tasks SET "order" = (SELECT COUNT(*) FROM tasks t2 WHERE t2.todo_id = tasks.todo_id AND t2.id < tasks.id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
