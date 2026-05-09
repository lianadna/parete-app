<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::connection($this->connection)->create('data_warga', function (Blueprint $collection) {
            $collection->unique('id_keluarga');
            $collection->unique('nama_pengguna', null, null, ['sparse' => true]);
            $collection->index('nomor_rumah');
            $collection->index('status_akun');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('data_warga');
    }
};
