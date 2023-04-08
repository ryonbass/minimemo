<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemoController extends Controller
{
    public function index(Request $request)
    {
        $memo = new Memo;
        $user_id = Auth::id();
        $data = $memo->showRegisteredAllMemo($user_id);
        return view('memo/index', $data);
    }
    public function update(Request $request)
    {
        $user_id = Auth::id();
        return view('memo/index', ["id" => $user_id]);
    }
    public function save(Request $request)
    {
        $memo = new Memo;
        $user_id = Auth::id();
        $memo->memoSave($request, $user_id);
        return;
    }
    public function delete(Request $request)
    {
        $memo = new Memo;
        $memo->memoDelete($request);
        return;
    }
}
