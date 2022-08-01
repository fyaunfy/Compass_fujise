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
                'old_year' => 'required',
                'old_month' => 'required',
                'old_day' => 'required',
                'role' => 'required',
                'password' => 'required|string|min:8|max:30|confirmed',
        ]);
    }
    // $this->validator($data); にまとめてあるものにあたる

        /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    // 新規登録 DBに情報を送る
    protected function create(array $request_data)
    {
        return User::create([
            'over_name' => $request_data['over_name'],
            'under_name' => $request_data['under_name'],
            'over_name_kana' => $request_data['over_name_kana'],
            'under_name_kana' => $request_data['under_name_kana'],
            'sex' => $request_data['sex'],
            'birth_day' => $request_data['birth_day'],
            'role' => $request_data['role'],
            'mail_address' => $request_data['mail_address'],
            // bcrypt [暗号学的ハッシュ関数]、セキュリティ対策
            'password' => bcrypt($request_data['password']),
        ]);
    }

    //新規登録
    public function registerPost(Request $request)
    {

        DB::beginTransaction();
        try{

            $old_year = $request->old_year;
            $old_month = $request->old_month;
            $old_day = $request->old_day;
            $data = $old_year . '-' . $old_month . '-' . $old_day;
            $birth_day = date('Y-m-d', strtotime($data));
            $subjects = $request->subject;
            $password = $request->password;

            // 空の$dataに inputで入力した値を入れる
            $request_data = $request->input();

            // バリデーションメソッドを$validatorの変数に入れる
            $validator = $this->validator($request_data);

            // もし$validatorの値のルールと送られていた値が違ったらregisterに返す。
            // エラー文も送る。
            if ($validator->fails()) {
                return redirect('/register')
                    ->withErrors($validator)
                    ->withInput();
            }

            // 新規登録を実行
            $user_get = $this->create($request_data);
  
            // // 入れた値を登録
            // $user_get = User::create([
            //     'over_name' => $request->over_name,
            //     'under_name' => $request->under_name,
            //     'over_name_kana' => $request->over_name_kana,
            //     'under_name_kana' => $request->under_name_kana,
            //     'mail_address' => $request->mail_address,
            //     'sex' => $request->sex,
            //     'birth_day' => $request->birth_day,
            //     'role' => $request->role,
            //     'password' => bcrypt($request->password)
            // ]);

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