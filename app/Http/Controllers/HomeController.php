<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Removed duplicate import

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Memo; // Correctly import the Memo model

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //メモ一覧を取得する
        $memos = Memo::where('user_id', Auth::id())->get();
        dd($memos);
        return view('home');
    }

    public function create()
    {
        //ログインしているユーザーの情報をviewに渡す
        $user = Auth::user();
        return view('create', compact('user'));
    }

    //Requwestクラスを使うとホームに入力されたメモやIDをうけとれる
    //POSTメソッドで送信されたデータを受け取る
    public function store(Request $request)
    {

        $data = $request->all(); // POSTされたデータを取得
        // dd($data); // Removed to prevent script termination
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

        // 同じタグがあるか確認
        // $exist_tag = Tag::where('name', $data['tag'])->where('user_id', $data['user_id'])->first();
        // if( empty($exist_tag['id']) ){
        //     //先にタグをインサート
        //     $tag_id = Tag::insertGetId(['name' => $data['tag'], 'user_id' => $data['user_id']]);
        // }else{
        //     $tag_id = $exist_tag['id'];
        // }
        //タグのIDが判明する
        // タグIDをmemosテーブルに入れてあげる
        // メモをmemosテーブルに挿入し、メモIDを取得
        $memo_id = Memo::insertGetId([
            //カラムとデータが一致している
            'content' => $data['content'], // メモの内容
            'user_id' => $data['user_id'], // ユーザーID
            // 'tag_id' => $tag_id,           // タグID
            'status' => 1                 // ステータス（1: 有効）
        ]);

        // リダイレクト処理
        return redirect()->route('home');
    }
}
