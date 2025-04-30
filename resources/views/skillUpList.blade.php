@extends('layouts.app') {{-- あなたのレイアウトに応じて変更 --}}

@section('content')
<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-6">日々の自己研鑽一覧</h1>

  @if($totalSkillUpTime->count() > 0)
  <table class="w-full border border-gray-300 text-left">
    <thead class="bg-gray-100">
      <tr>
        <th class="py-2 px-4 border-b">日付</th>
        <th class="py-2 px-4 border-b">総勉強時間（分）</th>
        <th class="py-2 px-4 border-b">判定</th>
      </tr>
    </thead>
    <tbody>
      @foreach($totalSkillUpTime as $record)
      <tr class="hover:bg-gray-50">
        <td class="py-2 px-4 border-b">{{ $record->date }}</td>
        <td class="py-2 px-4 border-b">{{ $record->total_minutes }}</td>
        <td class="py-2 px-4 border-b">
          @if($record->judge_flag === '0')
          <span class="text-green-600 font-semibold">優</span>
          @else
          <span class="text-red-600 font-semibold">劣</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-6">
    {{ $totalSkillUpTime->links() }} {{-- ページネーションリンク --}}
  </div>
  @else
  <p class="text-center text-gray-500 mt-10">記録がありません。</p>
  @endif
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