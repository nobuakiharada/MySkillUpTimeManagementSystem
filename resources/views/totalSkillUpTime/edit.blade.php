@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">研鑽記録の編集</h1>

  <form action="{{ route('skillUpResult.update', $record->date) }}" method="POST"
    class="max-w-md mx-auto bg-white p-6 rounded shadow">
    @csrf
    @method('post')

    {{-- 日付表示（編集不可） --}}
    <div class="mb-4">
      <label class="block text-gray-700 font-medium mb-2">日付</label>
      <input type="text" value="{{ $record->date }}" class="w-full px-4 py-2 border rounded bg-gray-100" readonly>
    </div>

    {{-- 時間入力 --}}
    @php
    $hours = floor($record->total_minutes / 60);
    $minutes = $record->total_minutes % 60;
    @endphp
    <div class="mb-4">
      <label class="block text-gray-700 font-medium mb-2">総学習時間</label>
      <div class="flex gap-2">
        <input type="number" name="hours" value="{{ $hours }}" class="w-1/2 px-4 py-2 border rounded" min="0"> 時間
        <input type="number" name="minutes" value="{{ $minutes }}" class="w-1/2 px-4 py-2 border rounded" min="0"
          max="59"> 分
      </div>
    </div>

    <div class="mt-6 text-center">
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500">
        更新する
      </button>
      <a href="{{ route('skillUpResult') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400">
        キャンセル
      </a>
    </div>
  </form>
</div>
@endsection