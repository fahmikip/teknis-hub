<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('document_number')->nullable();
            $table->foreignId('document_type_id')->constrained('document_types')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->restrictOnDelete();

            $table->unsignedInteger('year');
            $table->date('document_date')->nullable();
            $table->string('status')->default('draft');
            $table->string('access_level')->default('internal');
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('title');
            $table->index('document_number');
            $table->index('year');
            $table->index('status');
            $table->index('access_level');
            $table->index('category_id');
            $table->index('stage_id');
            $table->index('document_type_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
