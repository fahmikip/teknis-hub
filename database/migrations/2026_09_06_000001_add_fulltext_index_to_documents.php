<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE documents ADD FULLTEXT INDEX documents_fulltext (title, document_number, description, keywords)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('documents', function () {
                DB::statement('ALTER TABLE documents DROP INDEX documents_fulltext');
            });
        }
    }
};
