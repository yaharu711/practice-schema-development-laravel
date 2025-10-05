<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        // 既に存在する場合は重複挿入を避ける
        if (Todo::query()->count() > 0) {
            return;
        }

        $now = now();
        $records = [
            ['id' => (string) Str::uuid(), 'title' => 'Sample todo 1', 'completed' => false, 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'title' => 'Sample todo 2', 'completed' => true,  'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'title' => 'Sample todo 3', 'completed' => false, 'created_at' => $now, 'updated_at' => $now],
        ];

        Todo::query()->insert($records);
    }
}

