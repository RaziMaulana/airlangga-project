<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE presensis ALTER COLUMN status TYPE VARCHAR(255);");
        DB::statement("ALTER TABLE presensis DROP CONSTRAINT IF EXISTS presensis_status_check;");
        DB::statement("ALTER TABLE presensis ADD CONSTRAINT presensis_status_check CHECK (status IN ('Hadir', 'Sakit', 'Izin'));");
        DB::statement("ALTER TABLE presensis ALTER COLUMN status SET DEFAULT 'Hadir';");
        DB::statement("ALTER TABLE presensis ALTER COLUMN status SET NOT NULL;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE presensis DROP CONSTRAINT IF EXISTS presensis_status_check;");
        DB::statement("ALTER TABLE presensis ALTER COLUMN status TYPE VARCHAR(255);");
        DB::statement("ALTER TABLE presensis ADD CONSTRAINT presensis_status_check CHECK (status IN ('IN', 'OUT'));");
        DB::statement("ALTER TABLE presensis ALTER COLUMN status SET DEFAULT 'IN';");
        DB::statement("ALTER TABLE presensis ALTER COLUMN status SET NOT NULL;");
    }
};
