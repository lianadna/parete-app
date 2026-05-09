<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::connection($this->connection)->create('informasi_rt', function (Blueprint $collection) {
            $collection->index('jenis_informasi');
            $collection->index('tanggal_publikasi');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('informasi_rt');
    }
};
