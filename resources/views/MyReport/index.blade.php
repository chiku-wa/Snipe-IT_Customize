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
