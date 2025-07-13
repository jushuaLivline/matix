<?php 
return [
    // messages
    'success_message' => "データは正常に登録されました",

    // confirmations
    'confirmations' => [
        'purchase_requisition_denial_confirmation' => "この購買申請を否認します、よろしいでしょうか？",
        'purchase_requisition_delete_approval_confirmation' => "現在選択されている承認者を除外します、よろしいでしょうか？",
        'purchase_requisition_create_new_approval_confirmation' => "この申請を次の承認者に依頼します、よろしいでしょうか？",
    ],

    // validations
    'validations' => [
        'required' => "は必須です",
        'remote' => "が存在しません", // this is for lookup with no found in database
        'minlength' => "{0}桁で入力してください。",
        'maxlength' => "{0}文字以内で入力してください",
        'date_format' => "正しい形式で入力してください",
        'past_date' => "未来日付を入力してください。",
    ],
];