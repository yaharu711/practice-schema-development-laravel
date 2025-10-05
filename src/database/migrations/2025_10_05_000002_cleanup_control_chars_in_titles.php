<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 制御文字(例: \x00-\x1F, \x7F) を含むタイトルを削除
        DB::table('todos')
            ->whereRaw("title ~ '[[:cntrl:]]'")
            ->delete();
    }

    public function down(): void
    {
        // 破壊的変更のためロールバック処理なし
    }
};

