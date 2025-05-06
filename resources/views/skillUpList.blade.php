@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold text-center text-gray-800 mb-8">日々の自己研鑽一覧</h1>
  @if(isset($message))
  <div class="mt-4 text-green-600 text-center font-semibold">
    {{ $message }}
  </div>
  @endif

  @if($totalSkillUpTime->count() > 0)
  <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-6">
    <table class="table-auto w-full text-sm text-left text-gray-700 border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border-b font-medium text-gray-800">日付</th>
          <th class="px-4 py-2 border-b font-medium text-gray-800">総勉強時間（分）</th>
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
            <span class="text-green-600 font-semibold">優</span>
            @else
            <span class="text-red-600 font-semibold">劣</span>
            @endif
          </td>
          <td class="px-4 py-2 border-b">
            <a href="{{ route('skillUpResult.edit', $record->date) }}" class="text-blue-600 hover:text-blue-800">編集</a>
          </td>
          <td class="px-4 py-2 border-b">
            <form action="{{ route('skillUpResult.destroy', $record->date) }}" method="POST"
              onsubmit="return confirm('本当に削除してもよろしいですか？');">
              @csrf
              <button type="submit" class="text-red-600 hover:text-red-800">削除</button>
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
    <div class="flex justify-start">
      <a href="{{ route('skillUpResult.register') }}"
        class="btn start bg-red-600 text-white hover:bg-red-500 focus:bg-red-700 active:bg-red-800 focus:ring-red-500 px-4 py-2 rounded">
        新規登録
      </a>
    </div>

    <div class="flex justify-end">
      <a href="{{ route('home') }}"
        class="bg-orange-600 text-white border-2 border-orange-600 px-4 py-2 rounded hover:bg-orange-500 focus:ring-2 focus:ring-orange-400">
        ホームへ戻る
      </a>
    </div>
  </div>
</div>
@endsection