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
        Schema::create('tvs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama TV
            $table->string('ip_address')->unique(); // IP address TV
            $table->string('location')->nullable(); // Lokasi TV
            $table->text('description')->nullable(); // Deskripsi TV
            $table->foreignId('playlist_id')->nullable()->constrained()->onDelete('set null'); // Playlist yang dipilih
            $table->boolean('is_active')->default(true); // Status aktif TV
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tvs');
    }
};
