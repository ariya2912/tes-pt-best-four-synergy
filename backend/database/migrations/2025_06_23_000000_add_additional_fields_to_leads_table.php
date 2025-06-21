<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('nomor')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('leasing')->nullable();
            $table->string('tenor')->nullable();
            $table->date('tanggal_kredit')->nullable();
            $table->string('asuransi')->nullable();
            $table->string('hargajual')->nullable();
            $table->string('discount')->nullable();
            $table->string('status')->nullable();
            $table->string('distribusi')->nullable();
            $table->string('salesman')->nullable();
            $table->string('followup')->nullable();
            $table->string('statusfollowup')->nullable();
            $table->date('tglfollowup')->nullable();
            $table->string('hasilfollowup')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'nomor', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'leasing', 'tenor', 'tanggal_kredit',
                'asuransi', 'hargajual', 'discount', 'status', 'distribusi', 'salesman', 'followup',
                'statusfollowup', 'tglfollowup', 'hasilfollowup'
            ]);
        });
    }
};
