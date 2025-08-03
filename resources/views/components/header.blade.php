<div class="flex flex-col items-center" style="padding-top: 32px;">
  <!-- タイトルは中央に配置 -->
  <h1 class="text-3xl font-bold text-center">
    {{ $title ?? 'タイトル未定義' }}
  </h1>

  <!-- ユーザーID表示エリア -->
  <div class="w-full mt-4 flex justify-end" style="padding-right: 10%;">
    <span class="text-lg text-gray-600">ユーザーID: {{ $userId ?? "????" }}</span>
  </div>

  <!-- ユーザーID表示エリア -->
  <div class="w-full mt-4 flex justify-end" style="padding-right: 10%;">
    <span class="text-lg text-gray-600">本日の総合計時間: {{ $totalStudyTime ?? "?" }}分</span>
  </div>
</div>