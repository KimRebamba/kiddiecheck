<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Rename children -> students
        if (Schema::hasTable('children') && !Schema::hasTable('students')) {
            DB::statement('RENAME TABLE `children` TO `students`');
        }

        // Pivot: teacher_child -> teacher_student
        if (Schema::hasTable('teacher_child') && !Schema::hasTable('teacher_student')) {
            DB::statement('RENAME TABLE `teacher_child` TO `teacher_student`');
        }

        // Tags: child_tags -> student_tags
        if (Schema::hasTable('child_tags') && !Schema::hasTable('student_tags')) {
            DB::statement('RENAME TABLE `child_tags` TO `student_tags`');
        }

        // Column renames where applicable
        // teacher_student: child_id -> student_id
        if (Schema::hasColumn('teacher_student', 'child_id') && !Schema::hasColumn('teacher_student', 'student_id')) {
            DB::statement('ALTER TABLE `teacher_student` CHANGE `child_id` `student_id` BIGINT UNSIGNED NOT NULL');
        }
        // student_tags: child_id -> student_id
        if (Schema::hasColumn('student_tags', 'child_id') && !Schema::hasColumn('student_tags', 'student_id')) {
            DB::statement('ALTER TABLE `student_tags` CHANGE `child_id` `student_id` BIGINT UNSIGNED NOT NULL');
        }
        // tests: child_id -> student_id, adjust unique
        if (Schema::hasColumn('tests', 'child_id') && !Schema::hasColumn('tests', 'student_id')) {
            // Drop unique index if exists
            try { DB::statement('ALTER TABLE `tests` DROP INDEX `tests_child_id_test_date_unique`'); } catch (\Throwable $e) {}
            DB::statement('ALTER TABLE `tests` CHANGE `child_id` `student_id` BIGINT UNSIGNED NOT NULL');
            try { DB::statement('ALTER TABLE `tests` ADD UNIQUE INDEX `tests_student_id_test_date_unique` (`student_id`, `test_date`)'); } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        // Reverse renames
        if (Schema::hasTable('students') && !Schema::hasTable('children')) {
            DB::statement('RENAME TABLE `students` TO `children`');
        }
        if (Schema::hasTable('teacher_student') && !Schema::hasTable('teacher_child')) {
            DB::statement('RENAME TABLE `teacher_student` TO `teacher_child`');
        }
        if (Schema::hasTable('student_tags') && !Schema::hasTable('child_tags')) {
            DB::statement('RENAME TABLE `student_tags` TO `child_tags`');
        }
        // Columns
        if (Schema::hasColumn('teacher_student', 'student_id') && !Schema::hasColumn('teacher_student', 'child_id')) {
            DB::statement('ALTER TABLE `teacher_student` CHANGE `student_id` `child_id` BIGINT UNSIGNED NOT NULL');
        }
        if (Schema::hasColumn('student_tags', 'student_id') && !Schema::hasColumn('student_tags', 'child_id')) {
            DB::statement('ALTER TABLE `student_tags` CHANGE `student_id` `child_id` BIGINT UNSIGNED NOT NULL');
        }
        if (Schema::hasColumn('tests', 'student_id') && !Schema::hasColumn('tests', 'child_id')) {
            try { DB::statement('ALTER TABLE `tests` DROP INDEX `tests_student_id_test_date_unique`'); } catch (\Throwable $e) {}
            DB::statement('ALTER TABLE `tests` CHANGE `student_id` `child_id` BIGINT UNSIGNED NOT NULL');
            try { DB::statement('ALTER TABLE `tests` ADD UNIQUE INDEX `tests_child_id_test_date_unique` (`child_id`, `test_date`)'); } catch (\Throwable $e) {}
        }
    }
};
