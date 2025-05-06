@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-center mt-100">自己研鑽-時間管理システム</h1>

@if(isset($message))
<div class="mt-4 text-green-600 text-center font-semibold">
  {{ $message }}
</div>
@endif

@php
$sessionTodaySkillUpTime = session('todaySkillUpTime');
@endphp

<div class="mt-6">
  <div class="flex justify-center gap-12">
    <form action="{{ route('today.finish', ['id' => $sessionTodaySkillUpTime->id]) }}" method="POST"
      class="w-full max-w-full">
      @csrf
      <input type="hidden" name="user_name" value="harada">
      <input type="hidden" name="user_id" value="1020">
      <input type="hidden" name="start_flag" value="0">
      <input type="hidden" name="end_flag" value="1">

      <!-- ボタンを囲むdiv -->
      <div class="flex justify-center gap-12 mb-8">
        {{-- 開始ボタン --}}
        <button class="bg-gray-600 text-white px-6 py-2 rounded-lg cursor-not-allowed opacity-60 w-32">
          開始中
        </button>

        {{-- 終了ボタン --}}
        <button type="submit"
          class="bg-blue-800 text-white hover:bg-blue-700 focus:bg-blue-900 active:bg-blue-900 focus:ring-blue-500 px-6 py-2 rounded-lg w-32">
          終了
        </button>
      </div>
      <!-- 自己研鑽内容のテキストボックス -->
      <div class="mb-6">
        <label for="study_content" class="block text-gray-700 font-medium mb-2">自己研鑽内容</label>
        <textarea id="study_content" name="study_content" rows="6"
          class="w-full resize-y p-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="例: 自己研鑽の内容を記載してください"></textarea>
        @error('study_content')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>
    </form>
  </div>
</div>

<div class="mt-6">
  {{-- todaySkillUpTimeRecord コンポーネントの呼び出し --}}
  <x-today-skill-up-time-record :todaySkillUpTimeAllRecords="$todaySkillUpTimeAllRecords" />
</div>

@endsection