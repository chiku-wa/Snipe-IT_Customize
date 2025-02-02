@component('mail::message')
    {{ trans_choice('mail.license_expiring_alert', $licenses->count(), ['count' => $licenses->count(), 'threshold' => $threshold]) }}
    @component('mail::table')
        <table width="100%">
            <tr>
                <td>&nbsp;</td>
                <td>
                  {{ # ヘッダに「所属名」を追加
                      trans('general.company')
                  }}
                </td>
                <td>{{ trans('mail.name') }}</td>
                <td>{{ trans('mail.Days') }}</td>
                <td>{{ trans('mail.expires') }}</td>
            </tr>
            @foreach ($licenses as $license)
                @php
                    $expires = Helper::getFormattedDateObject($license->expiration_date, 'date');
                    $diff = round(
                        abs(strtotime($license->expiration_date->format('Y-m-d')) - strtotime(date('Y-m-d'))) / 86400,
                    );
                    $icon = $diff <= $threshold / 2 ? '🚨' : ($diff <= $threshold ? '⚠️' : ' ');
                @endphp
                <tr>
                    <td>{{ $icon }} </td>
                    <td>
                      {{ /*
                          「app/Models/License.php」に追加したライセンス抽出用メソッドのSELECT句の別名「company_name」
                          に対応する列情報を記載する。
                        */
                          $license->company_name
                      }}
                    </td>
                    <td> <a href="{{ route('licenses.show', $license->id) }}">{{ $license->name }}</a> </td>
                    <td> {{ $diff }} {{ trans('mail.Days') }} </td>
                    <td>{{ $expires['formatted'] }}</td>
                </tr>
            @endforeach
        </table>
    @endcomponent
@endcomponent
