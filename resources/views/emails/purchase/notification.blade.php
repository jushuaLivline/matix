<!DOCTYPE html>
<html>
<head>
    <title>【承認確認】購買依頼について</title>
</head>
<body> 
    <p>下記購買依頼が作成されましたので承認確認をお願いいたします。</p>
    <p>
        購買依頼詳細：<a href="{{ config('app.url') . '/purchase/purchase-requisition/' . $id }}">{{ config('app.url') . '/purchase/purchase-requisition/' . $id }}</a>
    </p>
    <p>
        承認画面：<a href="{{ config('app.url') . '/purchase/purchase-requisition-approval-search' }}">{{ config('app.url') . '/purchase/purchase-requisition-approval-search' }}</a>
    </p>
</html>