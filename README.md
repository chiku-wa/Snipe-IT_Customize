# はじめに

OSS製品であるSnipe-ITの機能改修に関する手順をここにまとめる。
なお、Snipe-IT`v7.0.13-pre`を前提としている。

# 任意のSQLを実行してExcelダウンロードさせる機能を実装する

任意のSQLを実行し、その結果をExcelでダウンロードできる機能を実装する手順を記載する。

## 実装後のイメージ

### 作成するプログラムの仕様
本稿では、下記の構成であることを前提にする。

| 項目           | 値                         |
| -------------- | -------------------------- |
| コントローラ名 | MyReportController         |
| アクション     | assets_and_licenses_report |


### MVCフローチャート

MVCフローチャートは以下の通り。

```mermaid
graph TD;
    %% 自作Viewへの導線
    menu[Snipe-ITの標準メニュー<br>views/layouts/default.blade.php] -->|左のサイドバーからリンクをクリック|custom_controller_index[自作のコントローラ.アクション<br/>Controllers/MyReportController.index]-->|Excelの一覧画面を表示|custom_view[自作のView<br/>views/MyReport/index.blade.php];

    %%  自作ViewからExcelダウンロードへの導線
    custom_view-->|Excelの一覧画面からダウンロードリンクをクリック|custom_controller_[自作のコントローラ.アクション<br/>Controllers/MyReport.assets_and_licences_report]-->|ブラウザダウンロード|dl_excel[Excelダウンロード];
```

### 画面の操作イメージ
1. 左のサイドバーのレポート一覧に`カスタムレポート`リンクが追加される。

    <img src="images//README/image-1.png" width="20%">

2. リンクをクリックすると、カスタムレポートの一覧画面に遷移し、リンクをクリックするとExcelダウンロードが始まる。

    <img src="images/README/image-3.png" width="70%">

    ↓

    <img src="images/README/image-4.png" width="50%">

## ①Composerライブラリの導入
下記コマンドで必要なライブラリを導入しておくこと。
※後述の`php artisan make:export`コマンドを実行する際に必要となる

```bash
 composer require maatwebsite/excel
```


## ②エクスポートクラスを作成する
SQLを実行してExcel出力するためのエクスポートクラスを作成する。

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

app/Http/Controllers/MyReportController.php
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

        // 実行するSQLを定義
        $columnsStr = implode(',', $columns);
        $sql = <<<SQL
                    select
                        $columnsStr
                    from
                        assets a
                    left join license_seats ls on
                        a.id = ls.asset_id
                    left join licenses l on
                        ls.license_id = l.id
        SQL;

        // Excelエクスポート用オブジェクトを定義
        $sqlExportObj = new SqlExport(
            $sql,
            $headers
        );

        // Excelファイルのダウンロードを実行
        return Excel::download($sqlExportObj, 'assets.xlsx');
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

# サイドバーに新たに自作のカスタムレポート画面へのリンクを作成する

「[[/任意のSQLを実行してExcelダウンロードさせる機能を実装する]]」で作成したコントローラに新たに`index`アクションを追加し、Snipe-ITのサイドバーにリンクを追加してアクセスできるようにする。

## ①コントローラ、ルーティング作成
まずコントローラにアクションを追加し、ルーティングを追加する。

app/Http/Controllers/MyReportController.php

```php
・・・
    /**
     * カスタムレポートのダウンロード一覧画面を表示するアクションメソッド
     */
    public function index()
    {
        return view('MyReport.index');
    }
・・・
```

routes/web.php

```php
・・・
    Route::get('/MyReport', [MyReportController::class, 'index']);
・・・
```

また、後述のビューに埋め込むリンクで使用するため、レポートダウンロード用のルーティングに名前をつける。

routes/web.php

```php
・・・
    // 資産情報とライセンスを並列で出力するレポートのダウロード用アクション
    Route::get('/MyReport/assets_and_licences_report', [MyReportController::class, 'assets_and_licences_report'])
        ->name('myreport/assets_and_licences_report');　★ここの設定を追記する
・・・
```

## ②ラベルの定義
Snipe-ITでは、ラベルはあらかじめ所定のPHPに定義する必要がある。
下記を追記する。

resources/lang/ja-JP/general.php

```php
<?php

return [
・・・
    // 自作のカスタムレポートのラベル
    'my_report'        => 'カスタムレポート',
・・・
```

上記で定義したラベルは下記で呼び出すことができる。
```php
{{ trans('general.my_report') }}
```

## ③カスタムレポートの一覧ビューの作成

先ほど作成したコントローラへのリンクするビューを作成する。

以下のビューを作成すること。
resources/views/CustomReport/index.blade.php
```php
@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.my_report') }}
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="text-center col-md-12" style="padding-top: 10px;">
  <a href="{{ route('myreport/assets_and_licences_report') }}" class="btn btn-primary btn-sm" style="width: 100%">
    すべての資産情報とライセンスをまとめてExcelダウンロード
  </a>
</div>

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table')
@stop
```

※`{{ route('myreport/assets_and_licences_report') }}`は、「[[/コントローラ、ルーティング作成]]」で記述したルーティングの名前（`->name`）に対応する。

# 参考

## Snipe-ITのデザインを踏襲して新たなViewを作成する場合のポイント

そのままシンプルなHTMLを記述してしまうと、Snipe-ITのヘッダやフッタなどが表示されず、CSSも適用されないため、必ず下記の記述は漏れなく組み込んだうえでビューを作成すること。

```php
@extends('layouts/default')

{{-- Page title --}}
@section('title')
 ★★★任意のヘッダタイトル★★★
@parent
@stop

{{-- Page content --}}
@section('content')

★★★任意のHTMLを記述★★★

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table')
@stop
```

次にSnipe-ITに標準で実装されているサイドバーのリンクメニューに、

