<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = ['customers', 'service_types', 'transactions', 'payments', 'documents'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('route_key', 20)->nullable()->unique()->after('id');
            });

            DB::table($tableName)
                ->select('id')
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($tableName): void {
                    foreach ($rows as $row) {
                        do {
                            $key = bin2hex(random_bytes(10));
                        } while (DB::table($tableName)->where('route_key', $key)->exists());

                        DB::table($tableName)->where('id', $row->id)->update(['route_key' => $key]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->dropUnique($tableName.'_route_key_unique');
                $table->dropColumn('route_key');
            });
        }
    }
};
