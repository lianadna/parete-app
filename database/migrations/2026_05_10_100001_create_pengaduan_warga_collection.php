<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::connection($this->connection)->create('pengaduan_warga', function (Blueprint $collection) {
            $collection->unique('nomor_pengaduan');
            $collection->index('referensi_warga_id');
            $collection->index('status_pengaduan');
            $collection->index('topik');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('pengaduan_warga');
    }
};
