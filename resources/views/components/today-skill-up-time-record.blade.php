<div class="mt-6">
  {{-- 本日の自己研鑽情報を表形式での表示 --}}
  <table class="table-auto w-full text-center border-collapse border border-gray-300">
    <thead>
      <tr>
        <th class="border border-gray-300 px-4 py-2">項目</th>
        <th class="border border-gray-300 px-4 py-2">内容</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="border border-gray-300 px-4 py-2">日付</td>
        <td class="border border-gray-300 px-4 py-2">{{ $todaySkillUpTimeRecord['date'] }}</td>
      </tr>
      <tr>
        <td class="border border-gray-300 px-4 py-2">ユーザー名</td>
        <td class="border border-gray-300 px-4 py-2">{{ $todaySkillUpTimeRecord['user_name'] }}</td>
      </tr>
      <tr>
        <td class="border border-gray-300 px-4 py-2">開始時間</td>
        <td class="border border-gray-300 px-4 py-2">{{ $todaySkillUpTimeRecord['start_time'] }}</td>
      </tr>
      <tr>
        <td class="border border-gray-300 px-4 py-2">終了時間</td>
        <td class="border border-gray-300 px-4 py-2">{{ $todaySkillUpTimeRecord['end_time'] ?? '未設定' }}</td>
      </tr>
      <tr>
        <td class="border border-gray-300 px-4 py-2">総勉強時間</td>
        <td class="border border-gray-300 px-4 py-2">{{ $todaySkillUpTimeRecord['total_study_time'] ?? '未設定' }}</td>
      </tr>
    </tbody>
  </table>
</div>

{{-- study_contentを広めのテキストボックスで表示 --}}
<div class="mt-6">
  <label for="study_content" class="block text-lg font-medium">勉強内容</label>
  <textarea id="study_content" class="w-full h-40 border border-gray-300 p-2"
    readonly>{{ $todaySkillUpTimeRecord['study_content'] ?? '未設定' }}</textarea>
</div>