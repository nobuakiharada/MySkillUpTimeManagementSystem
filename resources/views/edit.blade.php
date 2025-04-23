@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>自己研鑽時間編集</h2>

        <form action="{{ route('today.update', $skillUpTime->id) }}" method="POST">
            @csrf
            @method('POST') <!-- POSTメソッドを使う -->
            <div class="form-group">
                <label for="user_name">ユーザー名</label>
                <input type="text" class="form-control" name="user_name" value="{{ $skillUpTime->user_name }}">
            </div>
            <div class="form-group">
                <label for="start_time">開始時間</label>
                <input type="time" class="form-control" name="start_time" value="{{ $skillUpTime->start_time }}">
            </div>
            <div class="form-group">
                <label for="end_time">終了時間</label>
                <input type="time" class="form-control" name="end_time" value="{{ $skillUpTime->end_time }}">
            </div>
            <div class="form-group">
                <label for="study_content">学習内容</label>
                <textarea class="form-control" name="study_content">{{ $skillUpTime->study_content }}</textarea>
            </div>
            <!-- 他のフィールドも同様に作成 -->

            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection