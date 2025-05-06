@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-xl">
  <h2 class="text-2xl font-semibold text-center text-gray-800 mb-8">日々の自己研鑽　新規登録</h2>

  <form action="{{ route('skillUpResult.store') }}" method="POST" class="bg-white p-6 rounded shadow-md">
    @csrf

    <div class="mb-4">
      <label for="date" class="block text-gray-700 font-semibold mb-2">日付</label>
      <input type="date" name="date" id="date" class="w-full border-gray-300 rounded px-4 py-2" required>
    </div>

    <div class="mb-4">
      <label for="total_minutes" class="block text-gray-700 font-semibold mb-2">総勉強時間（分）</label>
      <input type="number" name="total_minutes" id="total_minutes" class="w-full border-gray-300 rounded px-4 py-2"
        required>
    </div>

    <div class="flex justify-between mt-6">
      <button type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500 focus:ring-2 focus:ring-blue-400">
        登録
      </button>
      <a href="{{ route('home') }}" class="text-gray-600 hover:underline self-center">キャンセル</a>
    </div>
  </form>
</div>
@endsection