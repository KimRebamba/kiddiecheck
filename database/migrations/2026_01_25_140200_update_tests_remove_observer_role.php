<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop unique index by name if present
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tests` DROP INDEX `tests_child_id_test_date_observer_role_unique`');
        } catch (\Throwable $e) {}

        // Drop observer_role column if it exists (robust check via INFORMATION_SCHEMA)
        try {
            $exists = \Illuminate\Support\Facades\DB::select("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tests' AND COLUMN_NAME = 'observer_role'");
            if (!empty($exists) && ($exists[0]->c ?? 0) > 0) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tests` DROP COLUMN `observer_role`');
            }
        } catch (\Throwable $e) {}

        // Add unique index on (child_id, test_date) if not present
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tests` ADD UNIQUE INDEX `tests_child_id_test_date_unique` (`child_id`, `test_date`)');
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // Remove new unique index
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tests` DROP INDEX `tests_child_id_test_date_unique`');
        } catch (\Throwable $e) {}

        // Recreate observer_role column and original unique
        if (!Schema::hasColumn('tests', 'observer_role')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->enum('observer_role', ['teacher','family'])->nullable();
            });
        }
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tests` ADD UNIQUE INDEX `tests_child_id_test_date_observer_role_unique` (`child_id`, `test_date`, `observer_role`)');
        } catch (\Throwable $e) {}
    }
};
