@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold text-center mt-8">自己研鑽時間管理（履歴）</h1>

<table class="min-w-full mt-6 table-auto border-collapse">
  <thead>
    <tr class="bg-gray-200">
      <th class="py-2 px-4 border border-gray-300">ID</th>
      <th class="py-2 px-4 border border-gray-300">ユーザー名</th>
      <th class="py-2 px-4 border border-gray-300">日付</th>
      <th class="py-2 px-4 border border-gray-300">開始時間</th>
      <th class="py-2 px-4 border border-gray-300">終了時間</th>
      <th class="py-2 px-4 border border-gray-300">合計時間</th>
      <th class="py-2 px-4 border border-gray-300">ステータス</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($skillupRecords as $record)
    <tr class="bg-white">
      <td class="py-2 px-4 border border-gray-300">{{ $record->id }}</td>
      <td class="py-2 px-4 border border-gray-300">{{ $record->user_name }}</td>
      <td class="py-2 px-4 border border-gray-300">{{ $record->date }}</td>
      <td class="py-2 px-4 border border-gray-300">{{ $record->start_time }}</td>
      <td class="py-2 px-4 border border-gray-300">{{ $record->end_time ?? '未終了' }}</td>
      <td class="py-2 px-4 border border-gray-300">{{ $record->total_study_time }} 分</td>
      <td class="py-2 px-4 border border-gray-300">
        @if ($record->start_flag == 1)
        開始中
        @else
        終了
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection