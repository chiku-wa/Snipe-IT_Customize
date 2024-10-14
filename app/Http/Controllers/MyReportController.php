<?php

namespace App\Http\Controllers;

// DBアクセスに必要なライブラリ
use Illuminate\Support\Facades\DB;

// SQLで実行したデータセットをExcelでダウンロードさせるために必要なライブラリ
use App\Exports\SqlExport;
use Maatwebsite\Excel\Facades\Excel;

class MyReportController extends Controller
{
    /**
     * カスタムレポートのダウンロード一覧画面を表示するアクションメソッド
     */
    public function index()
    {
        return view('MyReport.index');
    }

    /**
     * 資産とライセンス情報を並列で抽出し、Excelでダウンロードさせるアクションメソッド。
     */
    public function assets_and_licences_report()
    {
        // テーブルのカラム名と、Excelに出力するヘッダ名の対応配列変数を定義
        // ※general.phpで機械的に抽出できない日本語名があるため（資産名など）、明示的に定義
        // ※general.phpで取得できる項目については、trans()メソッドを用いて取得
        $columnHeaderHash = [
            // テーブルカラム名 => Excelヘッダ名
            'asset_tag' => trans('general.asset_tag'),
            'name' => '資産名'
        ];
        // 連想配列からカラム名（Key）のみを抽出して配列変数に格納
        $columns = array_keys($columnHeaderHash);
        // 連想配列からExcelヘッダ名（Value）のみを抽出して配列変数に格納
        $headers = array_values($columnHeaderHash);

        // 実行するSQLを定義
        $columnsStr = implode(',', $columns);
        $sql = "SELECT
                    $columnsStr
                FROM
                    assets
        ";

        // Excelエクスポート用オブジェクトを定義
        $sqlExportObj = new SqlExport(
            $sql,
            $headers
        );

        // Excelファイルのダウンロードを実行
        return Excel::download($sqlExportObj, 'assets.xlsx');
    }
}
