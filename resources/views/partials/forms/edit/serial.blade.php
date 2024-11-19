<!-- Serial -->
{{-- ↓ここから↓

<div class="form-group {{ $errors->has('serial') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">{{ trans('admin/hardware/form.serial') }} </label>
    <div class="col-md-7 col-sm-12{{  (Helper::checkIfRequired($item, 'serial')) ? ' required' : '' }}">

↑ここまでコメントアウト↑--}}

{{-- 「type="text"→「type="hidden"」に変更 --}}
        <input class="form-control" type="hidden" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->serial) }}" />

{{-- ↓ここから↓
            {!! $errors->first('serial', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
↑ここまでコメントアウト↑--}}
