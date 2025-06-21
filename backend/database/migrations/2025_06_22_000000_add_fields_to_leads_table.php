<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'tipe')) {
                $table->string('tipe')->nullable();
            }
            if (!Schema::hasColumn('leads', 'warna')) {
                $table->string('warna')->nullable();
            }
            if (!Schema::hasColumn('leads', 'no_hp')) {
                $table->string('no_hp')->nullable();
            }
            if (!Schema::hasColumn('leads', 'tanggal')) {
                $table->date('tanggal')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'warna', 'no_hp', 'tanggal']);
        });
    }
};
