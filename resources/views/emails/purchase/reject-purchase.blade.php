<!DOCTYPE html>
<html>
<head>
    <title>購買依頼差し戻しについて</title>
</head>
<body>
    @if ($returned === true)
        <p>下記の理由により購買依頼が差し戻しされましたことをお知らせします。</p>
        <p>差し戻し理由</p>
    @else
        <p>下記の理由により購買依頼が否認されましたことをお知らせします。</p>
        <p>否認理由</p>
    @endif
    
    <p>{{$purchaseNotificationData['reason_for_denial']}}</p>
    <p>
        購買依頼詳細：<a href="{{ $requisition_url }}">{{ $requisition_url }}</a>
    </p>
</html>