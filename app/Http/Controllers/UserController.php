<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Folder;
use App\Models\User;
use App\Models\Set;
use App\Models\Card;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $user_model;
    protected $set_model;

    public function __construct(User $user, Set $set)
    {
        $this->user_model = $user;
        $this->set_model = $set;
    }


    public function register(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|unique:users|email',
                'password' => 'required|min:8',
                're_password' => 'required|min:8',
                'name' => 'required'
            ],
            [
                'email.required' => 'Không được để trống email',
                'password.required' => 'Không được để trống password',
                'name.required' => 'Không được để trống tên',
                'password.min' => 'Mật khẩu phải dài ít nhất 8 ký tự',
                're_password.min' => 'Mật khẩu nhập lại phải dài ít nhất 8 ký tự'
            ]
        );
        if ($request->re_password != $request->password) {
            echo "Mật khẩu nhập lại không đúng!";
        } else {
            $encrypted_password = bcrypt($request->password);
        }
        $user = new User;
        $user->email = $request->email;
        $user->password = $encrypted_password;
        $user->name = $request->name;
        $user->save();
        return [
            'status' => 1,
            'code' => 1,
            'msg' => 'Register successfully'
        ];
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ],
            [
                'email.required' => 'Không được để trống email',
                'password.required' => 'Không được để trống password',
                'password.min' => 'Mật khẩu phải dài ít nhất 8 ký tự'
            ]
        );
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $remember_token = Hash::make(Str::random() . time());
            $user = User::where('email', $request->email)->first();
            $user->remember_token = $remember_token;
            $user->save();
            return [
                'status' => 1,
                'code' => 1,
                'msg' => 'Login successfully',
                'data' => $user
            ];
        } else {
            return [
                'status' => 0,
                'code' => 12,
                'msg' => 'Email or password is incorrect'
            ];
        }
    }

    public function logout(Request $request)
    {
        $token = $request->header("token");
        $user = $this->user_model->isTokenExist($token);
        if ($user == null) {
            return [
                'status' => 0,
                'code' => 403,
                'msg' => 'No token found'
            ];
        } else {
            //logout user you want to, by id
            $userToLogout = User::find($user->id);
            Auth::setUser($userToLogout);
            $userToLogout->remember_token = "";
            $userToLogout->save();
            Auth::logout();
            //set again current user
            //Auth::setUser($user);
            return [
                'status' => 1,
                'code' => 1,
                'msg' => 'Logout successfully'
            ];
        }
    }


    public function recentSets(Request $request)
    {
        $token = $request->header('token');
        $user = $this->user_model->isTokenExist($token);
        if ($user == null) {
            return [
                'status' => 0,
                'code' => 403,
                'msg' => 'No token found'
            ];
        } else {
            $data = $this->set_model->recentSets($user->id);
            $sets = [];
            for ($i=0; $i<count($data); $i++) {
                $data[$i]['completed'] = 0.15;
            }
            return [
                'status' => 1,
                'code' => 1,
                'msg' => 'Get User\'s Info Successfully',
                'data' => $data
            ];
        }
    }

    public function userInfo(Request $request)
    {
        $token = $request->header("token");
        try{
            $user = $this->user_model->isTokenExist($token);
            if ($user == null) {
                return [
                    'status' => 0,
                    'code' => 403,
                    'msg' => 'No token found'
                ];
            } else {
                return [
                    'status' => 1,
                    'code' => 200,
                    'msg' => $user
                ];
            }
        }catch(Exception $e){
            return[
                'status' => 1,
                'code' => 500,
                'msg' => "",
                "err" => $e
            ];
        }
    }
}
