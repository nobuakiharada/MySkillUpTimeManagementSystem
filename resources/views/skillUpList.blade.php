@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold text-center text-gray-800 mb-8">日々の自己研鑽一覧</h1>

  @if (session('message'))
  <div class="mt-4 text-green-600 font-semibold ml-10">
    {{ session('message') }}
  </div>
  @endif

  @if(isset($monthlyTotalMinutes))
  @php
  $hours = floor($monthlyTotalMinutes / 60);
  $minutes = $monthlyTotalMinutes % 60;
  @endphp
  <div class="mt-4 mb-6 text-xl text-blue-600 font-semibold text-center ml-6">
    {{ \Carbon\Carbon::parse($selectedMonth)->format('Y年m月') }} の総学習時間：
    <span class="text-blue-700">{{ $hours }}時間{{ $minutes }}分</span>
  </div>
  @endif

  @if(isset($userId))
  <div class="text-right text-sm text-gray-600 mb-2">
    ユーザーID：{{ $userId }}
  </div>
  @endif

  @if($totalSkillUpTime->count() > 0)
  <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-6">
    <table class="table-auto w-full text-sm text-left text-gray-700 border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border-b font-medium text-gray-800">日付</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">総自己研鑽時間（分）</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">判定</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">編集</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">削除</th>
        </tr>
      </thead>
      <tbody>
        @foreach($totalSkillUpTime as $record)
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2 border-b">{{ $record->date }}</td>
          <td class="px-4 py-2 border-b">{{ $record->total_minutes }}</td>
          <td class="px-4 py-2 border-b">
            @if($record->judge_flag === '0')
            <span class="text-green-600 font-semibold">合格</span>
            @elseif($record->judge_flag === '1')
            <span class="text-red-600 font-semibold">努力不足</span>
            @else
            <span class="text-yellow-600 font-semibold">不正値</span>
            @endif
          </td>
          <td class="px-4 py-2 border-b">
            <a href="{{ route('skillUpResult.edit', $record->date) }}" class="text-blue-600 hover:text-blue-800">編集</a>
          </td>
          <td class="px-4 py-2 border-b">
            <form action="{{ route('skillUpResult.destroy', $record->date) }}" method="POST"
              onsubmit="return confirm('本当に総学習時間をリセットしてもよろしいですか？');">
              @csrf
              <button type="submit" class="text-red-600 hover:text-red-800">リセット</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $totalSkillUpTime->links() }}
  </div>
  @else
  <p class="text-center text-gray-500 mt-10">記録がありません。</p>
  @endif

  <div class="flex justify-between items-center mt-6">
    <div>
      <form method="GET" action="{{ route('skillUpResult') }}" class="flex items-center">
        <label for="month" class="mr-2 text-gray-700">月選択：</label>
        <select name="month" id="month" class="border rounded px-3 py-1" onchange="this.form.submit()">
          @foreach($months as $month)
          <option value="{{ $month }}" {{ $month == $selectedMonth ? 'selected' : '' }}>
            {{ \Carbon\Carbon::parse($month)->format('Y年m月') }}
          </option>
          @endforeach
        </select>
      </form>
    </div>
    <div>
      <a href="{{ route('home') }}"
        class="bg-orange-600 text-white border-2 border-orange-600 px-4 py-2 rounded hover:bg-orange-500 focus:ring-2 focus:ring-orange-400">
        ホームへ戻る
      </a>
    </div>
  </div>

  <div class="flex justify-end mt-4 space-x-16">
    <a href="{{ route('skillUpResult.uniqueButton', ['type' => 'unstudySave']) }}?month={{ $selectedMonth }}"
      class="bg-red-600 text-white border-2 border-red-600 px-4 py-2 rounded hover:bg-red-500 focus:ring-2 focus:ring-red-600">
      未研鑽日の登録
    </a>
    <a href="{{ route('skillUpResult.uniqueButton', ['type' => 'reRegister']) }}?month={{ $selectedMonth }}"
      onclick="return confirm('本当に実行してもよろしいですか？');"
      class="bg-red-800 text-white border-2 border-red-600 px-4 py-2 rounded hover:bg-red-700 focus:ring-2 focus:ring-red-600">
      自己研鑽情報の再登録
    </a>
  </div>
</div>
@endsection