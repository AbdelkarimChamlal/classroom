<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;



class Auth extends Model
{
    public $primaryKey = "email";
    //



    public static $request;

    function __construct($request=null){
        if($request!=null)
        $this->request = $request;
    }


    public function isIdTokenValid($id_token){
        $ch = curl_init();
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=".$id_token;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode == 200;
    }

    //get the refresh token from database if exists
    public function getRefreshCode($email){
        $auth = Auth::find($email);
        if($auth!=null){
            return $auth->refresh_token;
        }
        return null;
    }

    //check if the access token is valid
    public function isAccessTokenValid($acces_token){
        $ch = curl_init();
        $url = "https://oauth2.googleapis.com/tokeninfo?access_token=".$acces_token;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode == 200;
    }

    //get the user information from his id token most important one is the email
    public function getUserInformation($id_token){
        $ch = curl_init();
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=".$id_token;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $head;
    }

    //exchange the autherization code for a access token and refresh token and also the id token
    public function exchangeCodeForAccessToken($code){
        $client_id = "94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com";
        $client_secret = "1yEYELh90BQELyTV2WTqOyws";
        $grant_type = "authorization_code";
        $redirect_url = "http://localhost/classroom/public/0auth.php";
        //send post request to google token api to refresh the access token
        $curl = curl_init();
        $body = "client_id=".$client_id."&client_secret=".$client_secret."&grant_type=".$grant_type."&code=".$code."&redirect_uri=".$redirect_url;
        curl_setopt($curl , CURLOPT_URL , "https://oauth2.googleapis.com/token");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($httpCode!=200){
            //this means that the refresh token is not valid anymore
            return null;
        }
        //this is when the token is updated successfully
        return $head;
    }

    //this function refresh the 
    public function refreshAccessToken($email,$refresh_token){
        //must have information to create a valid request
        $client_id = "94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com";
        $client_secret = "1yEYELh90BQELyTV2WTqOyws";
        $grant_type = "refresh_token";
        //send the post request
        $curl = curl_init();
        $body = "client_id=".$client_id."&client_secret=".$client_secret."&grant_type=".$grant_type."&refresh_token=".$refresh_token;
        curl_setopt($curl , CURLOPT_URL , "https://oauth2.googleapis.com/token");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        //if the reponse code isn't 200 that means the refresh code isn't valid anymore
        if($httpCode!=200){
            //this means that the refresh token is not valid anymore
            return null;
        }
        //return the new access token and id token
        return $head;
    }

    public function updateRefreshToken($email,$refresh_token){
        $result = Auth::find($email);
        $request = $this->request;
        if($result==null){
            $auth = new Auth();
            $auth->email = $email;
            $auth->refresh_token = $refresh_token;
            $auth->save();
        }else{
            $result->refresh_token = $refresh_token;
            $result->save();
        }
    }

    public function removeRefreshToken($email){
        Auth::destroy($email);
    }

    //check if the user signed in from the session data and validate the id token every step of the way
    public function isSigned(){
        $request = $this->request;
        if($request->session()->has('id_token')){
            $id_token = $request->session()->get('id_token');
            $auth = $this;
            if($auth->isIdTokenValid($id_token)){
                $data = $auth->getUserInformation($id_token);
                $data = json_decode($data);
                $request->session()->put('email',$data->email);
                return true;
            }else if($request->session()->has('email')){
                $email = $request->session()->get('email');
                $refresh_code = $auth->getRefreshCode($email);
                if($refresh_code==null){
                    $request->session()->flush();
                    return false;
                }
                $data = $auth->refreshAccessToken($email,$refresh_code);
                if($data!=null){
                    $data = json_decode($data);
                    $request->session()->put('id_token',$data->id_token);
                    $request->session()->put('access_token',$data->access_token);
                    $user = $auth->getUserInformation($data->id_token);
                    // $request->session()->put('name',$user->name);
                    return true;
                }else{
                    $auth->removeRefreshToken($email);
                }
            }
        }
        $request->session()->flush();
        return false;
    }

    public function hasAccess(){
        $request = $this->request;
        if($request->session()->has('email') || $request->session()->has('access_token')){
            $auth = $this;
            if($request->session()->has('access_token')){
                $access_token = $request->session()->get('access_token');
                if($auth->isAccessTokenValid($access_token)){
                    return true;
                }
            }

            //try to refresh the access token from database if the user has a refresh token saved in database
            
            if($request->session()->has('email')){
                $email = $request->session()->get('email');
                $refresh_code = $auth->getRefreshCode($email);
                if($refresh_code==null){
                    return false;
                }
                $data = $auth->refreshAccessToken($email,$refresh_code);
                if($data!=null){
                    $data = json_decode($data);
                    $request->session()->put('id_token',$data->id_token);
                    $request->session()->put('access_token',$data->access_token);
                    $user = $auth->getUserInformation($data->id_token);
                    $user=json_decode($user);
                    // $request->session()->put('name',$user->name);
                    return true;
                }else{
                    $auth->removeRefreshToken($email);
                }
            }
        }
        return false;
    }


    public function belong($email){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_token = $this->request->session()->get('access_token');
        $url = "https://www.googleapis.com/admin/directory/v1/users/".$email."?key=".$api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$access_token));
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode==200;
    }

    public function userInfo($email){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_token = $this->request->session()->get('access_token');
        $url = "https://www.googleapis.com/admin/directory/v1/users/".$email."?key=".$api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$access_token));
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $head;
    }


}
