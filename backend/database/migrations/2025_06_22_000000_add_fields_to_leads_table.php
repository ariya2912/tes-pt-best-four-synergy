<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('tipe')->nullable();
            $table->string('warna')->nullable();
            $table->string('no_hp')->nullable();
            $table->date('tanggal')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'warna', 'no_hp', 'tanggal']);
        });
    }
};
