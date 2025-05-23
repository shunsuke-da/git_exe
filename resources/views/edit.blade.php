{{-- layoutsフォルダのapp.bladeを親ファイルとする --}}

@extends('layouts.app')

{{-- @section~@endsectionまでが@yieldのcontentに対応 --}}

@section('content')
<div class="row justify-content-center ml-0 mr-0 h-100">
    {{ $user['name'] }}さんのメモ
    <div class="card w-100">
        <div class="card-header">
            メモ編集
            <form method='POST' action="/delete/{{$memo['id']}}" id='delete-form'>
                @csrf
                <!-- ゴミ箱ボタン→deletメソッドが走る（fontawesome + iタグ）<i id='delete-button' class="fas fa-trash"></i> -->
                <button class='p-0 ml-auto' style='border:none; float: right;'><i id='delete-button' class="fas fa-trash"></i></button>
            </form>  
        </div>
        <div class="card-body">
            <form method='POST' action="{{ route('update', ['id' => $memo['id'] ] ) }}">
                {{-- メモのIDを隠しフィールドとして送信 --}}
                {{-- CSRF対策のためのトークンを埋め込む --}}
                @csrf
                <input type='hidden' name='user_id' value="{{ $user['id'] }}">
                <div class="form-group">
                     <textarea name='content' class="form-control"rows="10">{{ $memo['content'] }}</textarea>
                </div>
                <div class="form-group">
                    <select class='form-control' name='tag_id'>
                @foreach($tags as $tag)
                    {{-- タグのIDを選択肢として表示 --}}
                    {{--三項演算子、 $tag['id']がメモのtag_idと一致する場合、selected属性を付与 、一致しなければ空白--}}
                    <option value="{{ $tag['id'] }}" {{ $tag['id'] == $memo['tag_id'] ? "selected" : "" }}>{{$tag['name']}}</option>
                @endforeach
                    </select>
                </div>
                <button type='submit' class="btn btn-primary btn-lg">更新</button>
            </form>
        </div>
    </div>
</div>
@endsection
