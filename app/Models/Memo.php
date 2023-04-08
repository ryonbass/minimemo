<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    function showRegisteredAllMemo($user_id)
    {
        $memo_data = $this::where('user_id', $user_id)->get();
        $data = !empty($memo_data) ? ["id" => $user_id, "memo_data" => $memo_data] : ["id" => $user_id];
        return $data;
    }
    function memoDelete($request)
    {
        $delete_id = $request->all();
        $this::where('id', $delete_id)->delete();
    }
    function memoSave($request, $user_id)
    {
        $memo_data = $request->all();
        $width = str_replace("px", "", $memo_data["width"]);
        $height = str_replace("px", "", $memo_data["height"]);
        $left = str_replace("px", "", $memo_data["left"]);
        $top = str_replace("px", "", $memo_data["top"]);
        Memo::create([
            "id" => $memo_data["id"],
            "user_id" => $user_id,
            "content" => $memo_data["content"],
            "color" => $memo_data["color"],
            "width" => $width,
            "height" => $height,
            "position_left" => $left,
            "position_top" => $top,
        ]);
    }
}
