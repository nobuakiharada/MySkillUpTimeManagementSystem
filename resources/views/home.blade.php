@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-center mt-100">自己研鑽-時間管理システム</h1>
<p class="text-center mt-4">本日の自己研鑽を開始しましょう</p>

<div class="flex justify-center gap-4 mt-6">
  {{-- 開始ボタン --}}
  <form action="{{ route('today.store') }}" method="POST">
    @csrf
    <input type="hidden" name="user_name" value="harada">
    <input type="hidden" name="user_id" value=1020>
    <input type="hidden" name="date" value="{{ now()->toDateString() }}">
    <input type="hidden" name="start_time" value="{{ now()->format('H:i') }}">
    <input type="hidden" name="start_flag" value="1">
    <input type="hidden" name="break_flag" value="0">
    <input type="hidden" name="end_flag" value="0">

    <button type="submit"
      class="btn start bg-red-600 text-white hover:bg-red-500 focus:bg-red-700 active:bg-red-800 focus:ring-red-500 px-4 py-2 rounded">
      開始
    </button>
  </form>

  {{-- 終了ボタン --}}
  <form action="{{ route('today.store') }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="1020">
    <input type="hidden" name="user_name" value="harada">
    <input type="hidden" name="date" value="{{ now()->toDateString() }}">
    <input type="hidden" name="end_time" value="{{ now()->format('H:i') }}">
    <input type="hidden" name="start_flag" value="0">
    <input type="hidden" name="break_flag" value="0">
    <input type="hidden" name="end_flag" value="1">

    <button type="submit"
      class="btn end bg-blue-800 text-white hover:bg-blue-700 focus:bg-blue-900 active:bg-blue-900 focus:ring-blue-500 px-4 py-2 rounded">
      終了
    </button>
  </form>
</div>
@endsection