<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 改行含み、空文字、または空白のみの title を削除
        DB::table('todos')
            ->where('title', '=','')
            ->orWhere('title', 'like', "%\n%")
            ->orWhere('title', 'like', "%\r%")
            // 空白のみ（非空白文字が0）の行
            ->orWhereRaw("regexp_replace(title, '\\s', '', 'g') = ''")
            ->delete();
    }

    public function down(): void
    {
        // 破壊的クリーンアップのためロールバックしない
    }
};

