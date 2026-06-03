<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::connection($this->connection)->create('profil_rt', function (Blueprint $collection) {
            $collection->unique('kunci');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('profil_rt');
    }
};
