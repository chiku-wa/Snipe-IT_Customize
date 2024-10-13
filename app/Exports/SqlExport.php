<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

// Excelにヘッダを付与するためのライブラリ
use Maatwebsite\Excel\Concerns\WithHeadings;

class SqlExport implements FromCollection, WithHeadings
{
    // ----- プロパティ変数
    protected $query;
    protected $headings;

    // ----- コンストラクタ
    // SQLクエリとヘッダを受け取る
    public function __construct($query, $headings = [])
    {
        $this->query = $query;
        $this->headings = $headings;
    }

    // ----- 各種メソッド
    /**
     * プロパティ変数のSQLを実行し、コレクションとして返す
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // クエリを実行して結果を返す
        return collect(DB::select($this->query));
    }

    /**
     * Excelのヘッダを配列として返す。
     *
     * @return array
     */
    public function headings(): array
    {
        // プロパティ変数のヘッダ情報を返す。もしプロパティ変数が存在しなれば空の配列を返す
        return $this->headings ?: [];
    }
}
