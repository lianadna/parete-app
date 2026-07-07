<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        $collection = DB::connection($this->connection)->getCollection('admin_pengguna');

        foreach ($collection->listIndexes() as $index) {
            if (($index->getName() ?? '') === 'email_1') {
                $collection->dropIndex('email_1');
                break;
            }
        }

        $collection->updateMany(
            ['email' => null],
            ['$unset' => ['email' => '']]
        );
    }

    public function down(): void
    {
        $collection = DB::connection($this->connection)->getCollection('admin_pengguna');

        try {
            $collection->createIndex(['email' => 1], ['unique' => true, 'name' => 'email_1']);
        } catch (\Throwable) {
            // Index may already exist or collection empty.
        }
    }
};
