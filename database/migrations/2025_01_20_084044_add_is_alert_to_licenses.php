<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーション実行時に、カラムを追加する処理
     */
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            // 末尾に追加するため、after(は省略する)
            $table->boolean('isAlert')->default(false);
        });
    }

    /**
     * マイグレーションの処理を切り戻す際に、カラムを削除する処理
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn('isAlert');
        });
    }
};
