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
        Schema::create('transaction_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // Original filename
            $table->string('file_path'); // Storage path
            $table->bigInteger('file_size'); // File size in bytes
            $table->string('mime_type'); // MIME type
            $table->uuid('uuid')->unique(); // Add this missing column
            $table->timestamps();

            $table->index(['transaction_id']);
            $table->index(['uuid']); // Add index for UUID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_attachments');
    }
};
