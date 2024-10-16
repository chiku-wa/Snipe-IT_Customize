カスタマイズしたSnipe-ITを管理するためのリポジトリ。

# カスタムレポートの追加方法
Snipe-ITにカスタムなレポート機能を追加し、任意のSQLを実行してExcelでダウンロードさせるまでの手順を記載する。

## ①前提
### 必要なComposerライブラリ
下記コマンドで必要なライブラリを導入しておくこと。

```bash
 composer require maatwebsite/excel
```

### 作成するプログラムの使用
具体例を示すため、本著では下記の公正であることを前提に記載する。

コントローラの構成：

| 項目           | 値                         |
| -------------- | -------------------------- |
| コントローラ名 | MyReportController         |
| アクション     | assets_and_licenses_report |


## コントローラの作成
## ②エクスポートクラスを作成する
SQLを実行してExcel出力するために必要な、エクスポートクラスを作成する。
以下のコマンドを実行してクラスを作成する。
```bash
php artisan make:export SqlExport
```

生成されたエクスポートクラスを以下の通り修正する。

app/Exports/SqlExport.php
```php
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
```

## ③コントローラ作成
下記コマンドでコントローラを作成する。

```bash
php artisan make:controller MyReportController
```

作成したコントローラを以下の通り修正する。
```php
<?php

namespace App\Http\Controllers;

// DBアクセスに必要なライブラリ
use Illuminate\Support\Facades\DB;

// SQLで実行したデータセットをExcelでダウンロードさせるために必要なライブラリ
use App\Exports\SqlExport;
use Maatwebsite\Excel\Facades\Excel;

class MyReportController extends Controller
{
    //
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

        // // ビューにデータを渡す
        // return view('assets_and_licences_report', ['results' => $results]);
    }
}
```

## ④ルーティング設定
下記ファイルを編集し、ルーティングを編集する。

routes/web.php
```php
<?php
・・・
★↓追記
// 自作のカスタムレポートのルーティング情報
use App\Http\Controllers\MyReportController;
・・・

Route::group(['middleware' => ['auth']], function () {
・・・
	★↓追記
    // 自作のカスタムレポートのルーティング情報
    Route::get('/MyReport/assets_and_licences_report', [MyReportController::class, 'assets_and_licences_report']);
・・・
```

上記を追記したら、以下のアドレスにアクセスする。
http://<IPアドレス>/MyReport/assets_and_licences_report

## 参考：レポートのカスタマイズ方法
コントローラの以下の記述を修正すれば良い。

app/Http/Controllers/MyReportController.php
```php
・・・
        $columnHeaderHash = [
            // テーブルカラム名 => Excelヘッダ名
            'asset_tag' => trans('general.asset_tag'),
            'name' => '資産名'
        ];
        // 連想配列からカラム名（Key）のみを抽出して配列変数に格納
        $columns = array_keys($columnHeaderHash);
        // 連想配列からExcelヘッダ名（Value）のみを抽出して配列変数に格納
        $headers = array_values($columnHeaderHash);
・・・
        // 実行するSQLを定義
        $columnsStr = implode(',', $columns);
        $sql = "SELECT
                    $columnsStr
                FROM
                    assets
        ";

```
