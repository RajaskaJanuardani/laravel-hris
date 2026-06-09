<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('karyawan')) {
            if ($this->hasIndex('karyawan', 'karyawan_karyawan_id_department_id_index')) {
                Schema::table('karyawan', function (Blueprint $table) {
                    $table->dropIndex('karyawan_karyawan_id_department_id_index');
                });
            }

            foreach (['department_id', 'position_id', 'shift_time_id'] as $column) {
                $foreignKey = "karyawan_{$column}_foreign";

                if (Schema::hasColumn('karyawan', $column) && $this->hasForeignKey('karyawan', $foreignKey)) {
                    Schema::table('karyawan', function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign($foreignKey);
                    });
                }
            }

            $legacyColumns = array_values(array_filter(
                ['department_id', 'position_id', 'shift_time_id'],
                fn (string $column): bool => Schema::hasColumn('karyawan', $column)
            ));

            if ($legacyColumns !== []) {
                Schema::table('karyawan', function (Blueprint $table) use ($legacyColumns) {
                    $table->dropColumn($legacyColumns);
                });
            }
        }

        Schema::dropIfExists('departments');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('shift_times');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is not recommended because the removed master data is intentionally retired.
    }

    private function hasIndex(string $table, string $index): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return true;
        }

        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    private function hasForeignKey(string $table, string $foreignKey): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return true;
        }

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $foreignKey)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
