<?php

namespace App\Http\Controllers\Authenticated\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Gate;
use App\Models\Users\User;
use App\Models\Users\Subjects;
use App\Searchs\DisplayUsers;
use App\Searchs\SearchResultFactories;

// これがないとコントローラーでAuthは使えない。
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    
    public function showUsers(Request $request){
        $keyword = $request->keyword;
        $category = $request->category;
        $updown = $request->updown;
        $gender = $request->sex;
        $role = $request->role;
        $subjects = null;// ここで検索時の科目を受け取る
        $userFactory = new SearchResultFactories();
        $users = $userFactory->initializeUsers($keyword, $category, $updown, $gender, $role, $subjects);
        $subjects = Subjects::all();
        // dd($subjects);

        $subject_lists = Subjects::with('users')->get();
        // dd($subject_lists);

        return view('authenticated.users.search', compact('users', 'subjects','subject_lists'));
    }

    public function userProfile($id){
        $user = User::with('subjects')->findOrFail($id);
        $subject_lists = Subjects::all();
        return view('authenticated.users.profile', compact('user', 'subject_lists'));
    }

    public function userEdit(Request $request){
        $user = User::findOrFail($request->user_id);
        $user->subjects()->sync($request->subjects);
        return redirect()->route('user.profile', ['id' => $request->user_id]);
    }

    // @foreach($post->postComments as $comment)
    // <div class="comment_area border-top">
    //   <p>
    //     <span>{{ $comment->commentUser($comment->user_id)->over_name }}</span>
    //     <span>{{ $comment->commentUser($comment->user_id)->under_name }}</span>さん
    //   </p>
    //   <p>{{ $comment->comment }}</p>
    // </div>
    // @endforeach



}     
