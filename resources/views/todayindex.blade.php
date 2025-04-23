@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>自己研鑽時間 一覧</h2>

        <!-- 成功メッセージ -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ユーザー名</th>
                    <th>日付</th>
                    <th>開始時間</th>
                    <th>終了時間</th>
                    <th>総学習時間</th>
                    <th>学習内容</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todaySkillUpTimes as $skillUpTime)
                    <tr>
                        <td>{{ $skillUpTime->user_name }}</td>
                        <td>{{ $skillUpTime->date }}</td>
                        <td>{{ $skillUpTime->start_time }}</td>
                        <td>{{ $skillUpTime->end_time }}</td>
                        <td>{{ $skillUpTime->total_study_time }} 分</td>
                        <td>{{ $skillUpTime->study_content }}</td>
                        <td>
                            <a href="{{ route('today.edit', $skillUpTime->id) }}" class="btn btn-primary">編集</a>
                        </td>
                        <td>
                            <form action="{{ route('today.destroy', $skillUpTime->id) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('today.store') }}" class="btn btn-success">新規登録</a>
    </div>
@endsection