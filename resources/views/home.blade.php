<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勉強管理システム - ホーム</title>
    <link rel="stylesheet" href="{{ asset('css/work.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>自己研鑽-時間管理システム</h1>
    <p>本日の自己研鑽の開始や終了をクリックしてください</p>

    <div class="buttons">
        <button class="btn start" onclick="sendAction('start')">開始</button>
        <button class="btn end" onclick="sendAction('end')">終了</button>
    </div>

    <div id="status"></div>

    <script src="{{ asset('js/work.js') }}"></script>
</body>
</html>