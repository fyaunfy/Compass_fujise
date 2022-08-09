<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Users\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;

use App\Models\Users\Subjects;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function registerView()
    {
        $subjects = Subjects::all();
        return view('auth.register.register', compact('subjects'));
    }

        /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $request_data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // バリデーションメソッドの作成 ルール定義
    protected function validator(array $request_data)
    {
        return Validator::make($request_data, [
                // バリデーションルール定義
                'over_name' => 'required|string|max:10',
                'under_name' => 'required|string|max:10',
                'over_name_kana' => 'required|string|regex:/^[ア-ン゛゜ァ-ォャ-ョー]+$/u|max:30',
                'under_name_kana' => 'required|string|regex:/^[ア-ン゛゜ァ-ォャ-ョー]+$/u|max:30',
                'mail_address' => 'required|string|email|max:100|unique:users',
                'sex' => 'required',
                'old_year' => 'required|after:2000',
                'old_month' => 'required',
                'old_day' => 'required',
                'role' => 'required',
                'password' => 'required|string|min:8|max:30|confirmed',
        ]);
    }
    // $this->validator($request_data); にまとめてあるものにあたる


    //新規登録
    public function registerPost(Request $request)
    {

        //エラーが発生したり、例外が投げられた場合は処理をなかったことにして、
        // トランザクションの開始前に戻します。
        // 簡単に言うと、処理のまとまり
        DB::beginTransaction();
        try{

            $old_year = $request->old_year;
            $old_month = $request->old_month;
            $old_day = $request->old_day;
            $data = $old_year . '-' . $old_month . '-' . $old_day;
            $birth_day = date('Y-m-d', strtotime($data));
            $subjects = $request->subject;

            // 空の$dataに inputで入力した値を入れる
            $request_data = $request->input();
            
            // dd($request_data);
            // // バリデーションメソッドを$validatorの変数に入れる
            $validator = $this->validator($request_data);

            // もし$validatorの値のルールと送られていた値が違ったらregisterに返す。
            // エラー文も送る。
            if ($validator->fails()) {
                return redirect('/register')
                    ->withErrors($validator)
                    ->withInput();
            }

            // 新規登録を実行
            $user_get = User::create([
                'over_name' => $request->over_name,
                'under_name' => $request->under_name,
                'over_name_kana' => $request->over_name_kana,
                'under_name_kana' => $request->under_name_kana,
                'mail_address' => $request->mail_address,
                'sex' => $request->sex,
                'birth_day' => $birth_day,
                'role' => $request->role,
                'password' => bcrypt($request->password)
            ]);


            // findOrFailメソッドは一致するIDが見つからなければエラーを返す役割
            $user = User::findOrFail($user_get->id);
            //多対多のリレーションで結合
            $user->subjects()->attach($subjects);

            //データベースにセーブ
            DB::commit();
            return view('auth.login.login');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('loginView');
        }
    }

}