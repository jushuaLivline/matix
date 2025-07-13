<?php
namespace App\Constants;

class Constant{

    //承認方法
    public const APPROVAL_METHOD_CATEGORY = [
        1 => 'システム',
        2 => '依頼書',
    ];

    public const TAX_CLASSIFICATION = [
        1 => '非課税',
        2 => '課税'
    ];

    //見積書
    public const QUOTATION_EXISTENCE_FLAG = [
        0 => '無し',
        1 => '有り'
    ];

    public const STATE_CLASSIFICATION = [
        0 => '依頼中',
        1 => '承認中',
        2 => '承認済',
        3 => '発注済',
        4 => '入荷済',
        9 => '否認'
    ];

    public const PURCHASE_REQUISITION_SEARCH_PURPOSE = [
        1 =>  "通常承認",  // ONly for employee that he is the next_approver.
        2 => "未到達分を繰上げ承認", // employee that he/she is part of the approvers.
        3 => "承認取消" // only the items that has his/her approval that needs to be canceled.
    ];

    public const PAGINATION_THRESHOLD = 20; // Set pagination threshold
}