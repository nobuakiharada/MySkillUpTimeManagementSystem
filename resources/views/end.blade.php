@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-center mt-100">自己研鑽-時間管理システム</h1>
@if(isset($message))
<div class="mt-4 text-green-600 text-center font-semibold">
  {{ $message }}
</div>
@endif
<div class="flex flex-col justify-center items-center mt-6">
  <p class="text-lg text-center text-gray-600 mb-8">
    学習を始める場合は「ホームへ戻る」、これまでの記録を確認したい場合は「一覧表示」をクリックしてください。
  </p>

  {{-- ボタン群 --}}
  <div class="flex justify-center gap-4">
    {{-- ホームへ戻るボタン --}}
    <a href="{{ route('home') }}"
      class="bg-orange-600 text-white border-2 border-orange-600 px-4 py-2 rounded hover:bg-orange-500 focus:ring-2 focus:ring-orange-400">
      ホームへ戻る
    </a>

    {{-- 一覧表示ボタン --}}
    <a href="{{ route('today.list') }}"
      class="bg-green-800 text-white border-2 border-green-800 px-4 py-2 rounded hover:bg-green-700 focus:ring-2 focus:ring-green-600">
      一覧表示
    </a>
  </div>

  {{-- PHPコードを使って時間と分に分割して表示 --}}
  @php
  $totalMinutes = $totalStudyTime; // コントローラから渡された総分数
  $hours = floor($totalMinutes / 60); // 時間部分
  $minutes = $totalMinutes % 60; // 分部分
  @endphp

  <div class="mt-6">
    <p class="text-6xl font-bold text-center text-blue-600 w-full leading-tight">
      総自己研鑽時間: {{ $hours }}時間{{ $minutes }}分
    </p>
  </div>

  <div class="mt-6 text-center">
    <h2 class="text-4xl font-semibold text-gray-800 mb-6 w-full leading-tight">
      本日もお疲れさまでした！！
    </h2>
  </div>
  @endsection