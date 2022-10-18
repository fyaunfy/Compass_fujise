<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'like_user_id',
        'like_post_id'
    ];

    public function users(){
        return $this->belongsToMany('App\Models\Users\User', 'likes', 'like_post_id', 'like_user_id');
    }

    //いいねの数
    public function likeCounts($post_id){
        // like_post_id(いいねした投稿のID)を取得し数をカウント
        return $this->where('like_post_id', $post_id)->get()->count();
    }



}