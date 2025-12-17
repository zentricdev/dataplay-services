<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync', function(Blueprint $table): void {
            $table->string('key_hash')->primary();
            $table->string('data_hash')->index();
            $table->string('status')->index()->default('new');
            $table->boolean('is_active')->index()->default(true);
            $table->jsonb('data');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync');
    }
};
