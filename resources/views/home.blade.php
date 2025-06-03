@extends('layouts.app')

@section('content')
<!-- configにて -->
<x-header :title="'自己研鑽 - 時間管理システム'" />

@if(session('breakMessage'))
<div class="text-center mt-4 text-green-600 font-semibold">
  {{ session('breakMessage') }}
</div>
@elseif(session('message'))
<div class="text-center mt-4 text-green-600 font-semibold">
  {{ session('message') }}
</div>
@endif

<div class="flex flex-col items-center gap-4 mt-6">
  {{-- 無効な開始ボタン（開始中） --}}
  @if(session('justNow'))
  <form action="{{ route('today.finish', ['id' => $newSkillUpTimeRecord->id ?? '' ]) }}" method="POST"
    class="flex flex-col items-center mb-8 w-full">
    @csrf
    <input type="hidden" name="user_name" value="harada">
    <input type="hidden" name="user_id" value="1020">
    <input type="hidden" name="start_flag" value="0">
    <input type="hidden" name="end_flag" value="1">

    {{-- 自己研鑽内容入力欄 --}}
    <div class="mb-8 w-full flex justify-center">
      <div class="w-full max-w-2xl px-4">
        <label for="study_content" class="block text-gray-700 font-semibold text-base mb-2 text-start">
          自己研鑽内容
        </label>
        <textarea id="study_content" name="study_content" rows="4"
          class="w-full resize-y p-4 text-base border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm"
          placeholder="例: 本日の自己研鑽内容を簡潔に記載してください"></textarea>
        @error('study_content')
        <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
        @enderror
      </div>
    </div>

    {{-- ボタン（開始：無効、終了：有効 or 無効） --}}
    <div class="flex justify-center gap-4">
      {{-- 開始ボタンは常に無効 --}}
      <button class="bg-red-600 text-white px-6 py-2 rounded-lg opacity-50 cursor-not-allowed w-32" disabled>
        開始
      </button>

      {{-- 終了ボタン（休憩中は無効） --}}
      @if(!empty($newSkillUpTimeRecord) && $newSkillUpTimeRecord->break_flag === '1')
      <button type="button" class="bg-blue-800 text-white px-6 py-2 rounded-lg opacity-50 cursor-not-allowed w-32"
        disabled>
        終了
      </button>
      @else
      <button type="submit"
        class="bg-blue-800 text-white hover:bg-blue-700 focus:bg-blue-900 active:bg-blue-900 focus:ring-blue-500 px-6 py-2 rounded-lg w-32">
        終了
      </button>
      @endif
    </div>
  </form>

  {{-- 休憩ボタン --}}
  <div class="flex justify-center gap-4 mb-6">
    @if(!empty($newSkillUpTimeRecord) && $newSkillUpTimeRecord->break_flag === '0')
    <form action="{{ route('today.break', ['id' => $newSkillUpTimeRecord->id]) }}" method="POST">
      @csrf
      <button type="submit" class="bg-yellow-500 text-white hover:bg-yellow-400 px-6 py-2 rounded-lg w-32">
        休憩開始
      </button>
    </form>
    @elseif(!empty($newSkillUpTimeRecord) && $newSkillUpTimeRecord->break_flag === '1')
    <form action="{{ route('today.break', ['id' => $newSkillUpTimeRecord->id]) }}" method="POST">
      @csrf
      <button type="submit" class="bg-yellow-500 text-white hover:bg-yellow-400 px-6 py-2 rounded-lg w-32">
        休憩終了
      </button>
    </form>
    @endif
  </div>

  @else
  {{-- 研鑽開始前のフォーム --}}
  <form action="{{ route('today.store') }}" method="POST" class="flex justify-center gap-4 mb-4">
    @csrf
    <input type="hidden" name="user_name" value="harada">
    <input type="hidden" name="user_id" value="1020">
    <input type="hidden" name="date" value="{{ now()->toDateString() }}">
    <input type="hidden" name="start_flag" value="1">
    <input type="hidden" name="break_flag" value="0">
    <input type="hidden" name="end_flag" value="0">

    {{-- 開始ボタン --}}
    <button type="submit" class="bg-red-600 text-white hover:bg-red-500 px-6 py-2 rounded-lg w-32">
      開始
    </button>

    {{-- 終了ボタン（無効） --}}
    <button type="button" class="bg-blue-800 text-white px-6 py-2 rounded-lg opacity-50 cursor-not-allowed w-32"
      disabled>
      終了
    </button>
  </form>
  @endif
</div>



<div class="mt-6 px-20">
  @if(session()->has('todaySkillUpTimeAllRecords'))
  {{-- セッションに今日の研鑽データがある場合の処理 --}}
  <x-today-skill-up-time-record :todaySkillUpTimeAllRecords="session('todaySkillUpTimeAllRecords')" />
  @else
  <p class="text-center text-gray-500">本日はまだ自己研鑽しておりません。</p>
  @endif
</div>

<div class="flex justify-end gap-4 mt-6 mb-4 w-full pr-6">
  <a href="{{ route('today.list') }}"
    class="bg-green-800 text-white border-2 border-green-800 px-4 py-2 rounded hover:bg-green-700 focus:ring-2 focus:ring-green-600">
    一覧表示
  </a>

  <a href="{{ route('skillUpResult') }}"
    class="bg-orange-600 text-white border-2 border-orange-600 px-4 py-2 rounded hover:bg-orange-500 focus:ring-2 focus:ring-orange-400">
    日々の研鑽まとめ
  </a>
</div>
@endsection