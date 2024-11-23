<?php

namespace App\Http\Controllers;

// DBアクセスに必要なライブラリ
use App\Exports\SqlExport;

// SQLで実行したデータセットをExcelでダウンロードさせるために必要なライブラリ
use Illuminate\Support\Facades\DB;

// SQLのクエリビルダを使用するためのライブラリ
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
            'a.name' => '資産名',
            'l.name' => trans("general.license"),
        ];
        // 連想配列からカラム名（Key）のみを抽出して配列変数に格納
        $columns = array_keys($columnHeaderHash);
        // 連想配列からExcelヘッダ名（Value）のみを抽出して配列変数に格納
        $headers = array_values($columnHeaderHash);

        //クエリビルダでSQLを生成し、文字列に変換して変数に格納する
        $sqlStr = DB::table('assets as a')
            ->select($columns)
            ->leftJoin(
                'license_seats as ls'
                , 'a.id', '=', 'ls.asset_id'
            )
            ->leftJoin(
                'licenses as l'
                , 'ls.license_id', '=', 'l.id'
            )
            ->toSql()
        ;

        // Excelエクスポート用オブジェクトを定義
        $sqlExportObj = new SqlExport(
            $sqlStr,
            $headers
        );

        // Excelファイルのダウンロードを実行
        return Excel::download($sqlExportObj, 'assets.xlsx');
    }
}
