<div class="mt-6">
  <!-- タイトルの追加 -->
  <h2 class="text-3xl font-semibold text-center mb-4">本日の自己研鑽状況（最新５件）</h2>

  {{-- 自己研鑽情報の表 --}}
  <table class="table-auto w-full text-center border-collapse border border-gray-300 rounded-lg shadow-lg">
    <thead>
      <tr>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">ID</th>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">日付</th>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">ユーザー名</th>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">開始時間</th>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">終了時間</th>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">総研鑽時間</th>
        <th class="border border-gray-300 px-6 py-3 bg-gray-100 text-lg">研鑽内容</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($todaySkillUpTimeAllRecords as $todayRecord)
      <tr>
        <td class="border border-gray-300 px-4 py-3">{{ $todayRecord->id }}</td>
        <td class="border border-gray-300 px-4 py-3">{{ $todayRecord->date }}</td>
        <td class="border border-gray-300 px-4 py-3">{{ $todayRecord->user_name }}</td>
        <td class="border border-gray-300 px-4 py-3">{{ $todayRecord->start_time }}</td>
        <td class="border border-gray-300 px-4 py-3 
          {{ $todayRecord->end_time === null || $todayRecord->end_time === '未設定' ? 'text-red-600 font-bold' : '' }}">
          {{ $todayRecord->end_time ?? '未設定' }}
        </td>
        <td class="border border-gray-300 px-4 py-3">{{ $todayRecord->total_study_time ?? '未設定' }}</td>
        <td class="border border-gray-300 px-4 py-3 text-left whitespace-pre-wrap">
          {{ $todayRecord->study_content ?? '未設定' }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>