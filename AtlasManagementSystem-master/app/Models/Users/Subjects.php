<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

use App\Models\Users\User;

class Subjects extends Model
{
    const UPDATED_AT = null;


    protected $fillable = [
        'subject_id',
        'user_id',
    ];

        // 多対多　リレーション設定
        // 第一引数には使用するモデル
        // 第二引数には使用するテーブル名
        // 第三引数にはリレーションを定義しているモデルの外部キー名
        // 第四引数には結合するモデルの外部キー名
        
    // ユーザーと教科のリレーション
    public function users(){
        return $this->belongsToMany('App\Models\Users\Subjects', 'subject_users', 'subject_id', 'user_id');
    }


}