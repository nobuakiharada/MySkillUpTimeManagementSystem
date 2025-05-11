@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 mt-8 rounded-lg shadow">
  <h2 class="text-2xl font-bold mb-6 text-gray-800">
    {{ isset($skillUpTime) ? '自己研鑽時間 編集' : '自己研鑽時間 登録' }}
  </h2>

  <form action="{{ route('today.update', $skillUpTime->id ?? null) }}" method="POST">
    @csrf

    <!-- ユーザー名（変更不可） -->
    <div class="mb-4">
      <label for="user_name" class="block text-gray-700 font-medium mb-1">ユーザー名</label>
      <input type="text" name="user_name" id="user_name" readonly
        class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed"
        value="{{ old('user_name', $skillUpTime->user_name ?? '') }}">
    </div>

    <!-- ユーザーID（変更不可） -->
    <div class="mb-4">
      <label for="user_id" class="block text-gray-700 font-medium mb-1">ユーザーID</label>
      <input type="number" name="user_id" id="user_id" readonly
        class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed"
        value="{{ old('user_id', $skillUpTime->user_id ?? '') }}">
    </div>

    <!-- 日付（変更不可） -->
    <div class="mb-4">
      <label for="date" class="block text-gray-700 font-medium mb-1">日付</label>
      <input type="date" name="date" id="date" readonly
        class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed"
        value="{{ old('date', $skillUpTime->date ?? '') }}">
    </div>

    <!-- 開始時間 -->
    <div class="mb-4">
      <label for="start_time" class="block text-gray-700 font-medium mb-1">開始時間</label>
      <input type="time" name="start_time" id="start_time"
        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="{{ old('start_time', $skillUpTime->start_time ?? '') }}">
      @error('start_time')
      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- 終了時間 -->
    <div class="mb-4">
      <label for="end_time" class="block text-gray-700 font-medium mb-1">終了時間</label>
      <input type="time" name="end_time" id="end_time"
        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="{{ old('end_time', $skillUpTime->end_time ?? '') }}">
      @error('end_time')
      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- 学習内容 -->
    <div class="mb-4">
      <label for="study_content" class="block text-gray-700 font-medium mb-1">学習内容</label>
      <textarea name="study_content" id="study_content" rows="4"
        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('study_content', $skillUpTime->study_content ?? '') }}</textarea>
      @error('study_content')
      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <!-- ボタン -->
    <div class="flex justify-end mt-6">
      <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 transition">
        {{ isset($skillUpTime) ? '更新する' : '登録する' }}
      </button>
      <a href="{{ route('today.list') }}"
        class="ml-4 px-5 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-100 transition">
        キャンセル
      </a>
    </div>
  </form>
</div>
@endsection