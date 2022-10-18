<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'main_category',
    ];

    //　こちらを追加
    // 「１対多」の「多」側 → メソッド名は複数形
    public function subCategories(){
        // リレーションの定義
        return $this->hasMany('App\SubCategory');
    }

}