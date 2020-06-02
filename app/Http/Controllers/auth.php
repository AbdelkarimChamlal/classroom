<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth as Authentication;

class auth extends Controller
{
    //


    public function validator(Request $request){
        $auth = new Authentication($request);
        if($auth->isIdTokenValid($_POST['token'])){
            $request->session()->put('id_token',$_POST['token']);
            $user = $auth->getUserInformation($_POST['token']);
            $user = json_decode($user);
            $request->session()->put('email',$user->email);
        }
        return redirect('/');
    }

    public function auth0(Request $request){
        $code = $request->input('code');
        $auth = new Authentication($request);
        $result = $auth->exchangeCodeForAccessToken($code);
        if($result!=null){
            $data = json_decode($result);
            $user = $auth->getUserInformation($data->id_token);
            $user = json_decode($user);
            $email = $user->email;
            if(property_exists($data,'refresh_token')){
                $auth->updateRefreshToken($email,$data->refresh_token);
            }
            $request->session()->put('id_token',$data->id_token);
            $request->session()->put('email',$email);
            $request->session()->put('access_token',$data->access_token);
        }
        return redirect('/');
    }
}
