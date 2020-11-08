<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

use App\User;    // 追加

class TasksController extends Controller
{
    // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        //タスク一覧を取得
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
        $user = \Auth::user();
        $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
        $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        /* メッセージ一覧ビューでそれを表示
        return view('tasks.index', [
            'tasks' => $tasks,
        ]);*/
        // Welcomeビューでそれらを表示
        return view('tasks.index', $data);
    }

    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        $task = new Task;

        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        //バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        // タスクを作成
        $task = new Task;
        $task->user_id = auth()->id();  //追加
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // getでtasks/（任意のid）にアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        if (\Auth::id() === $task->user_id) {
        
        return view('tasks.show', [
            'task' => $task,
        ]);
        }
        return redirect('/');
    }

    // getでtasks/（任意のid）/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスク編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

     // putまたはpatchでtasks/（任意のid）にアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスクを更新
        if (\Auth::id() === $task->user_id) {
        $task->user_id = auth()->id();  //追加
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // deleteでtasks/（任意のid）にアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスクを削除
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、タスクを削除
        if (\Auth::id() === $task->user_id) {
        $task->delete();
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }
}