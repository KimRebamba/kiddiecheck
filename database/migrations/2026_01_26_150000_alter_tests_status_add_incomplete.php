<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tests` MODIFY `status` ENUM('pending','in_progress','completed','cancelled','terminated','incomplete') NOT NULL DEFAULT 'pending'");
        } else {
            // For non-MySQL (e.g., SQLite in local dev), Laravel's enum typically maps to a string/check;
            // controllers can already store 'incomplete'. No-op to avoid risky table rebuilds.
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `tests` MODIFY `status` ENUM('pending','in_progress','completed','cancelled','terminated') NOT NULL DEFAULT 'pending'");
        } else {
            // No-op for non-MySQL.
        }
    }
};
