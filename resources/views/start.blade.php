@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-center mt-100">自己研鑽-時間管理システム</h1>

@if(isset($message))
<div class="mt-4 text-green-600 text-center font-semibold">
  {{ $message }}
</div>
@endif

<div class="flex justify-center gap-4 mt-6">
  {{-- 開始ボタン --}}
  <button
    class="bg-red-500 text-black border-2 border-gray-600 px-4 py-2 rounded opacity-70 cursor-not-allowed filter blur-sm">
    開始中
  </button>

  {{-- 終了ボタン --}}
  <form action="{{ route('today.update', ['id' => $todaySkillUpTime->id]) }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="1020">
    <input type="hidden" name="user_name" value="harada">
    <input type="hidden" name="date" value="{{ now()->toDateString() }}">
    <input type="hidden" name="end_time" value="{{ now()->format('H:i') }}">
    <input type="hidden" name="start_flag" value="0">
    <input type="hidden" name="end_flag" value="1">

    <button type="submit"
      class="btn end bg-blue-800 text-white hover:bg-blue-700 focus:bg-blue-900 active:bg-blue-900 focus:ring-blue-500 px-4 py-2 rounded">
      終了
    </button>
  </form>
</div>

<div class="mt-6">
  {{-- todaySkillUpTimeRecord コンポーネントの呼び出し --}}
  <x-today-skill-up-time-record :todaySkillUpTimeRecord="$todaySkillUpTime" />
</div>

@endsection