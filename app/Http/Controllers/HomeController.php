<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Removed duplicate import

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Memo; // Correctly import the Memo model
use App\Models\Tag; // Correctly import the Tag model

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
        //ログインしているユーザーの情報をviewに渡す
        $user = Auth::user();
        // dd($user); // Removed to prevent script termination
        //メモ一覧を取得する
        //DESCがついているので、最新のメモが一番上に来る
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        // dd($memos);
        return view('home', compact('user', 'memos'));
    }

    public function create()
    {
        //ログインしているユーザーの情報をviewに渡す
        $user = Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('create', compact('user', 'memos'));
    }

    //Requwestクラスを使うとホームに入力されたメモやIDをうけとれる
    //POSTメソッドで送信されたデータを受け取る
    public function store(Request $request)
    {
        $data = $request->all(); // POSTされたデータを取得
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

        // 同じタグがあるか確認,
        // whereメソッドを使って、nameカラムがPOSTされたタグ名と一致するレコードを取得
        $exist_tag = Tag::where('name', $data['tag'])->where('user_id', $data['user_id'])->first();
        //emptyは、変数が空かどうかを調べる関数→空であればtrueを返す→タグのIDを作る
        if( empty($exist_tag['id']) ){
            //先にタグをインサート,タグテーブルにデータを挿入
            //insertGetIdは、挿入したレコードのIDを取得するためのメソッド(最初はタグのID＝1がとれる)、
            //とったIDをのちにメモテーブルに入れて、ひもづける
            $tag_id = Tag::insertGetId(['name' => $data['tag'], 'user_id' => $data['user_id']]);
        }else{
            // タグがすでに存在する場合は、そのIDを取得
            // $exist_tag['id']は、すでに存在するタグのIDを取得
            $tag_id = $exist_tag['id'];
        }
        // タグのIDが判明する
        // タグIDをmemosテーブルに入れてあげる
        // メモをmemosテーブルに挿入し、メモIDを取得
        $memo_id = Memo::insertGetId([
            //カラムとデータが一致している
            'content' => $data['content'], // メモの内容
            'user_id' => $data['user_id'], // ユーザーID
            'tag_id' => $tag_id,           // タグID
            'status' => 1                 // ステータス（1: 有効）
        ]);

        // リダイレクト処理
        return redirect()->route('home');
    }

    public function edit($id)
    {
        //ログインしているユーザーの情報をviewに渡す
        $user = Auth::user();
        //メモ一覧を取得する
        //whereでstatusが1のものを取得、ユーザーidで指定、ユーザーIDが今回のログインしているユーザーのIDと一致するものを取得
        $memo = Memo::where('status', 1)->where('id',$id)->where('user_id', $user['id'])
        //first()は、条件に一致する最初のレコードを取得
        ->first();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        // Laravelの view() メソッドは、指定されたビュー（Bladeテンプレート）をレンダリングして返します。
        // compact() は、変数名をキーとして、その値を持つ連想配列を作成します。editはviewの名前で、compactは変数をビューに渡すための便利な方法です。
        // ここでは、$memo、$user、$memos という変数をビューに渡しています。
        // これにより、ビュー内で変数を簡単に使用できるようになります。
        $tags = Tag::where('user_id', $user['id'])->get();
        return view('edit', compact('memo', 'user', 'memos', 'tags'));
    }
    //Requwestクラスを使うとホームに入力されたメモやIDをうけとれる
    public function update(Request $request, $id)
    {
        //ログインしているユーザーの情報をviewに渡す
        $inputs = $request->all();
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す
        //idはURLのパラメータから取得
        Memo::where('id', $id)->update([
            'content' => $inputs['content'], // メモの内容
            'tag_id' => $inputs['tag_id'], // ユーザーID
            'status' => 1                 // ステータス（1: 有効）
        ]);
        return redirect()->route('home');
    }
    public function delete(Request $request, $id)
    {
        //ログインしているユーザーの情報をviewに渡す
        $inputs = $request->all();
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す
        //idはURLのパラメータから取得
        Memo::where('id', $id)->update([
            //論理削除のため、ステータスを２に変更
            'status' => 2                 // ステータス（2: 無効）
        ]);
        return redirect()->route('home')->with('success', 'メモを削除が完了しました。');
    }
}
