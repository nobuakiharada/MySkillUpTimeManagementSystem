@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

  <!-- 日付セレクトフォーム -->
  <form method="GET" action="{{ route('today.list') }}" class="mb-6 text-center">
    <label for="date" class="mr-2 font-medium text-gray-700">表示日：</label>
    <input type="date" name="date" id="date" value="{{ old('date', $selectedDate) }}"
      class="border px-3 py-2 rounded" />
    <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
      表示
    </button>
  </form>

  <h2 class="text-2xl font-semibold text-center text-gray-800 mb-8">自己研鑽時間 一覧</h2>

  @if(session('changeMessage'))
  <div class="text-left mt-4 text-green-600 font-semibold">
    {{ session('changeMessage') }}
  </div>
  @elseif(session('message'))
  <div class="text-left mt-4 text-green-600 font-semibold">
    {{ session('message') }}
  </div>
  @endif

  @if(isset($totalRecord))
  @php
  $hours = floor($totalRecord->total_minutes / 60);
  $minutes = $totalRecord->total_minutes % 60;
  @endphp

  <!-- 上段：総学習時間 + 休憩時間 -->
  <div class="mb-2 flex justify-center items-center space-x-12">
    <!-- 総学習時間 -->
    <div class="text-xl text-blue-600 font-semibold">
      {{ $selectedDate }} の総学習時間：
      <span class="text-blue-700">{{ $hours }}時間{{ $minutes }}分</span>
    </div>

    <!-- 休憩時間（あれば） -->
    @isset($totalBreakTime)
    <div class="text-xl text-gray-600 font-semibold">
      休憩時間：
      <span class="text-gray-900">{{ $totalBreakTime }} 分</span>
    </div>
    @endisset
  </div>

  <!-- 下段：判定（右寄せ） -->
  <div class="mb-6 flex justify-end">
    <div class="text-lg font-semibold min-w-[120px]">
      判定：
      @if($totalRecord->judge_flag === '0')
      <span class="text-green-600">合格</span>
      @elseif($totalRecord->judge_flag === '1')
      <span class="text-red-600">努力不足</span>
      @else
      <span class="text-yellow-600">不正値</span>
      @endif
    </div>
  </div>
  @endif

  @if(isset($userId))
  <div class="text-right text-sm text-gray-600 mb-2">
    ユーザーID：{{ $userId }}
  </div>
  @endif

  <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-6">
    <table class="table-auto w-full text-sm text-left text-gray-700 border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">ID</th> <!-- ID列を追加 -->
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">日付</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">開始時間</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">終了時間</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">総学習時間</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">学習内容</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">編集</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800 text-center">削除</th>
        </tr>
      </thead>
      <tbody>
        @foreach($todaySkillUpTimes as $skillUpTime)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2 border-b text-center">{{ $skillUpTime->id }}</td> <!-- IDの表示 -->
          <td class="px-4 py-2 border-b text-center">{{ $skillUpTime->date }}</td>
          <td class="px-4 py-2 border-b text-center">{{ $skillUpTime->start_time }}</td>
          <td class="px-4 py-2 border-b text-center">{{ $skillUpTime->end_time }}</td>
          <td class="px-4 py-2 border-b text-center">{{ $skillUpTime->total_study_time }} 分</td>
          <td class="px-4 py-2 border-b text-center">{{ $skillUpTime->study_content }}</td>
          <td class="px-4 py-2 border-b text-center">
            <a href="{{ route('today.edit', $skillUpTime->id) }}" class="text-blue-600 hover:text-blue-800">編集</a>
          </td>
          <td class="px-4 py-2 border-b text-center">
            <form action="{{ route('today.destroy', $skillUpTime->id) }}" method="POST"
              onsubmit="return confirm('本当に削除しますか？');">
              @csrf
              @method('POST')
              <button type="submit" class="text-red-600 hover:text-red-800 mx-auto block">
                削除
              </button>
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
      <a href="{{ route('today.create') }}"
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