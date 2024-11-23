<?php

namespace App\Http\Controllers;

// DBアクセスに必要なライブラリ
use App\Exports\SqlExport;

// SQLクエリ（テーブル名）を作成する際に使用する各種モデルクラス
use App\models\Asset;
use App\models\License;
use App\Models\LicenseSeat;

// SQLのクエリビルダを使用するためのライブラリ
use Illuminate\Support\Facades\DB;

// SQLで実行したデータセットをExcelでダウンロードさせるために必要なライブラリ
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
        // SQLで使用する各種テーブル名を、モデルクラスから抽出する
        // ※文字列内でテーブル名を直書きしないようにするため
        $tableNameAsset = (new Asset())->getTable();
        $tableNameLicense = (new License())->getTable();
        $tableNameLicenseSeat = (new LicenseSeat())->getTable();

        // テーブルのカラム名と、Excelに出力するヘッダ名の対応配列変数を定義
        // ※general.phpで機械的に抽出できない日本語名があるため（資産名など）、明示的に定義
        // ※general.phpで取得できる項目については、trans()メソッドを用いて取得
        $columnHeaderHash = [
            // テーブルカラム名 => Excelヘッダ名
            "{$tableNameAsset}.name" => (trans('general.asset') . "名"),
            "{$tableNameLicense}.name" => trans('general.license'),
        ];
        // 連想配列からカラム名（Key）のみを抽出して配列変数に格納
        $columns = array_keys($columnHeaderHash);
        // 連想配列からExcelヘッダ名（Value）のみを抽出して配列変数に格納
        $headers = array_values($columnHeaderHash);

        //クエリビルダでSQLを生成し、文字列に変換して変数に格納する
        $sqlStr = DB::table($tableNameAsset)
            ->select($columns)
            ->leftJoin(
                $tableNameLicenseSeat
                , "{$tableNameAsset}.id", '=', "{$tableNameLicenseSeat}.asset_id"
            )
            ->leftJoin(
                $tableNameLicense
                , "{$tableNameLicenseSeat}.license_id", '=', "{$tableNameLicense}.id"
            )
            ->toSql()
        ;

        // Excelエクスポート用オブジェクトを定義
        $sqlExportObj = new SqlExport(
            $sqlStr,
            $headers
        );

        // Excelファイルのダウンロードを実行
        return Excel::download($sqlExportObj, '資産情報とライセンスの一覧.xlsx');
    }
}
