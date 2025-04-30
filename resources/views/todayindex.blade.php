@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h2 class="text-2xl font-semibold text-center text-gray-800 mb-8">自己研鑽時間 一覧</h2>

  <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-6">
    <table class="table-auto w-full text-sm text-left text-gray-700 border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border-b font-medium text-gray-800">ID</th> <!-- ID列を追加 -->
          <th class="px-4 py-2 border-b font-medium text-gray-800">ユーザー名</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">日付</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">開始時間</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">終了時間</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">総学習時間</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">学習内容</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">編集</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">削除</th>
        </tr>
      </thead>
      <tbody>
        @foreach($todaySkillUpTimes as $skillUpTime)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2 border-b">{{ $skillUpTime->id }}</td> <!-- IDの表示 -->
          <td class="px-4 py-2 border-b">{{ $skillUpTime->user_name }}</td>
          <td class="px-4 py-2 border-b">{{ $skillUpTime->date }}</td>
          <td class="px-4 py-2 border-b">{{ $skillUpTime->start_time }}</td>
          <td class="px-4 py-2 border-b">{{ $skillUpTime->end_time }}</td>
          <td class="px-4 py-2 border-b">{{ $skillUpTime->total_study_time }} 分</td>
          <td class="px-4 py-2 border-b">{{ $skillUpTime->study_content }}</td>
          <td class="px-4 py-2 border-b">
            <a href="{{ route('today.edit', $skillUpTime->id) }}" class="text-blue-600 hover:text-blue-800">編集</a>
          </td>
          <td class="px-4 py-2 border-b">
            <form action="{{ route('today.destroy', $skillUpTime->id) }}" method="POST"
              onsubmit="return confirm('本当に削除しますか？');">
              @csrf
              @method('POST')
              <button type="submit" class="text-red-600 hover:text-red-800">削除</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="flex justify-between items-center mt-6">
    <!-- 左下に新規登録ボタン -->
    <div class="flex justify-start">
      <a href="{{ route('register') }}"
        class="btn start bg-red-600 text-white hover:bg-red-500 focus:bg-red-700 active:bg-red-800 focus:ring-red-500 px-4 py-2 rounded">
        新規登録
      </a>
    </div>

    <!-- 右下にホームボタン -->
    <div class="flex justify-end">
      <a href="{{ route('home') }}"
        class="bg-orange-600 text-white border-2 border-orange-600 px-4 py-2 rounded hover:bg-orange-500 focus:ring-2 focus:ring-orange-400">
        ホームへ戻る
      </a>
    </div>
  </div>
</div>

@endsection