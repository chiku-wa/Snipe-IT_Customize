<?php

return [
    'bulk_delete'		=> '대량 자산 삭제 승인',
    'bulk_restore'      => 'Confirm Bulk Restore Assets', 
  'bulk_delete_help'	=> '아래의 대량 자산 삭제 내용을 검토하십시오. 삭제하시면 복구할 수 없고, 현재 할당되어 있는 사용자와의 연결이 끊어집니다.',
  'bulk_restore_help'	=> 'Review the assets for bulk restoration below. Once restored, these assets will not be associated with any users they were previously assigned to.',
  'bulk_delete_warn'	=> '삭제 대상: asset_count 개',
  'bulk_restore_warn'	=> '복원 대상: asset_count 개',
    'bulk_update'		=> '대량 자산 갱신',
    'bulk_update_help'	=> '이 양식은 한번에 여러개의 자산들을 갱신하게 해줍니다. 변경하고 싶은 항목만 채워 넣으세요. 빈란으로 남겨둔 항목들은 변경되지 않을 것입니다. ',
    'bulk_update_warn'	=> 'You are about to edit the properties of a single asset.|You are about to edit the properties of :asset_count assets.',
    'bulk_update_with_custom_field' => 'Note the assets are :asset_model_count different types of models.',
    'bulk_update_model_prefix' => 'On Models', 
    'bulk_update_custom_field_unique' => 'This is a unique field and can not be bulk edited.',
    'checkedout_to'		=> '반출 대상',
    'checkout_date'		=> '반출 일자',
    'checkin_date'		=> '반입 일자',
    'checkout_to'		=> '반출 대상',
    'cost'				=> '구매 원가',
    'create'			=> '자산 생성',
    'date'				=> '구매 일자',
    'depreciation'	    => '감가 상각',
    'depreciates_on'	=> '감가 상각 일자',
    'default_location'	=> '기본 장소',
    'default_location_phone' => '기본 유선 전화번호',
    'eol_date'			=> '폐기 일자',
    'eol_rate'			=> '폐기 비율',
    'expected_checkin'  => '반입 예상 일',
    'expires'			=> '만료',
    'fully_depreciated'	=> '감가상각필',
    'help_checkout'		=> '이 자산을 즉시 사용하려면, 위의 상태 목록에서 "사용 준비"를 선택하세요. ',
    'mac_address'		=> 'MAC 주소',
    'manufacturer'		=> '제조업체',
    'model'				=> '모델',
    'months'			=> '개월',
    'name'				=> '자산 명',
    'notes'				=> '비고',
    'order'				=> '주문 번호',
    'qr'				=> 'QR 코드',
    'requestable'		=> '사용자가 이 자산을 요청하실 수 있습니다',
    'redirect_to_all'   => 'Return to all :type',
    'redirect_to_type'   => 'Go to :type',
    'redirect_to_checked_out_to'   => 'Go to Checked Out to',
    'select_statustype'	=> '상태 유형 선택',
    'serial'			=> '일련번호',
    'status'			=> '상태',
    'tag'				=> '자산 태그',
    'update'			=> '자산 갱신',
    'warranty'			=> '보증',
        'warranty_expires'		=> '보증 만료일',
    'years'				=> '년',
    'asset_location' => 'Update Asset Location',
    'asset_location_update_default_current' => 'Update default location AND actual location',
    'asset_location_update_default' => 'Update only default location',
    'asset_location_update_actual' => 'Update only actual location',
    'asset_not_deployable' => 'That asset status is not deployable. This asset cannot be checked out.',
    'asset_not_deployable_checkin' => 'That asset status is not deployable. Using this status label will checkin the asset.',
    'asset_deployable' => 'That status is deployable. This asset can be checked out.',
    'processing_spinner' => 'Processing... (This might take a bit of time on large files)',
    'optional_infos'  => 'Optional Information',
    'order_details'   => 'Order Related Information'
];
