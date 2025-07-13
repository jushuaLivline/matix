<!DOCTYPE html>
<html>
<head>
    <title>【承認確認】購買依頼について</title>
</head>
<body>
    @if ($next === true)
    <p>下記購買依頼につきまして前任者の承認が完了しましたのでご確認をお願いいたします。</p>        
    @else
    <p>下記購買依頼が作成されましたので承認確認をお願いいたします。</p>
    @endif
    <p>
        購買依頼詳細：<a href="{{ $purchaseNotificationData['requisition_url'] }}">{{$purchaseNotificationData['requisition_url']}}</a>
    </p>
    <p>
        承認画面：<a href="{{ $purchaseNotificationData['approvals_search_url'] }}">{{ $purchaseNotificationData['approvals_search_url'] }}</a>
    </p>
</html>