@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold text-center mt-100">自己研鑽-時間管理システム</h1>
    <p class="text-center mt-4">本日の自己研鑽の開始や終了をクリックしてください</p>
    <div class="flex justify-center gap-4 mt-6">
        <x-primary-button
            class="btn start"
            :color="'bg-red-600 text-white hover:bg-red-500 focus:bg-red-700 active:bg-red-800 focus:ring-red-500'"
            onclick="sendAction('start')">開始</x-primary-button>
        <x-primary-button
            class="btn break"
            onclick="sendAction('break')">休憩 </x-primary-button>
        <x-primary-button
            class="btn end"
            :color="'bg-blue-800 text-white hover:bg-blue-700 focus:bg-blue-900 active:bg-blue-900 focus:ring-blue-500'"
            onclick="sendAction('end')">終了</x-primary-button>
    </div>
@endsection