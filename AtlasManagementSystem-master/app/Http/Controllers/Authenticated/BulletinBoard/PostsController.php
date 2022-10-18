<?php

namespace App\Http\Controllers\Authenticated\BulletinBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\MainCategory;
use App\Models\Categories\SubCategory;
use App\Models\Posts\Post;
use App\Models\Posts\PostComment;
use App\Models\Posts\Like;
use App\Models\Users\User;
use App\Http\Requests\BulletinBoard\PostFormRequest;
use Auth;

// validatorを使用
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    public function show(Request $request){
        $posts = Post::with('user', 'postComments')->get();
        $categories = MainCategory::get();
        $like = new Like;
        $post_comment = new Post;
        //こちらを追加した。
        $comment = new PostComment;

        $sub_categories = SubCategory::get();

        if(!empty($request->keyword)){
            $posts = Post::with('user', 'postComments')
            ->where('post_title', 'like', '%'.$request->keyword.'%')
            ->orWhere('post', 'like', '%'.$request->keyword.'%')->get();
        }else if($request->category_word){
            $sub_category = $request->category_word;
            $posts = Post::with('user', 'postComments')->get();
        }else if($request->like_posts){
            $likes = Auth::user()->likePostId()->get('like_post_id');
            $posts = Post::with('user', 'postComments')
            ->whereIn('id', $likes)->get();
        }else if($request->my_posts){
            $posts = Post::with('user', 'postComments')
            ->where('user_id', Auth::id())->get();
        }
        return view('authenticated.bulletinboard.posts', compact('posts', 'categories', 'like', 'post_comment', 'comment', 'sub_categories'));
    }

    public function postDetail($post_id){
        $post = Post::with('user', 'postComments')->findOrFail($post_id);
        return view('authenticated.bulletinboard.post_detail', compact('post'));
    }

    //カテゴリーの投稿
    public function postInput(){
        $main_categories = MainCategory::get();
        $sub_categories = SubCategory::get();
        return view('authenticated.bulletinboard.post_create', compact('main_categories','sub_categories'));
    }

    public function postCreate(PostFormRequest $request){
        $post = Post::create([
            'user_id' => Auth::id(),
            'post_title' => $request->post_title,
            'post' => $request->post_body
        ]);
        return redirect()->route('post.show');
    }

    //投稿編集
    public function postEdit(Request $request){

        $rules = [
            // バリデーションルール定義
            'post' => 'required|string|max:5000',
            'post_title' => 'required|string|max:100',
              ];
        // 引数の値がバリデートされればリダイレクト、されなければ引き続き処理の実行
        $this->validate($request, $rules);
      
        Post::where('id', $request->post_id)->update([
            'post_title' => $request->post_title,
            'post' => $request->post,
            
        ]);
        
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function postDelete($id){
        Post::findOrFail($id)->delete();
        return redirect()->route('post.show');
    }

    //メインのカテゴリーの新規登録
    public function mainCategoryCreate(Request $request){
        MainCategory::create(['main_category' => $request->main_category_name]);
        return redirect()->route('post.input');
    }

    // サブカテゴリーの新規登録
    public function subCategoryCreate(Request $request){
        // dd($request);
        SubCategory::create(['sub_category' => $request->sub_category_name]);
        return redirect()->route('post.input');
    }

    //コメントの登録
    public function commentCreate(Request $request){

        PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);

       

        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    //ボード
    public function myBulletinBoard(){
        // ログインしているユーザーの記事の取得
        $posts = Auth::user()->posts()->get();
        $like = new Like;

        return view('authenticated.bulletinboard.post_myself', compact('posts', 'like'));
    }

    public function likeBulletinBoard(){
        
        $like_post_id = Like::with('users')->where('like_user_id', Auth::id())->get('like_post_id')->toArray();
        $posts = Post::with('user')->whereIn('id', $like_post_id)->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_like', compact('posts', 'like'));
    }

    // いいねする
    public function postLike(Request $request){

        //ログインしているユーザーがいいねしたとき
        Auth::user()->likes()->attach($request->post_id);
        // javaScriptに移動
        return response()->json();
    }

    //いいね解除
    public function postUnLike(Request $request){

        //ログインしているユーザーがいいねを解除したとき
        Auth::user()->likes()->detach($request->post_id);

        // javaScriptに移動
        return response()->json();
    }
}