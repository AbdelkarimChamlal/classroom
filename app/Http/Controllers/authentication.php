<?php

 //the auth part 
 $auth = new Auth($request);
 if(!$auth->isSigned()){
     return view('sign');
 }
 if(!$auth->hasAccess()){
     $user = $auth->getUserInformation($request->session()->get('id_token'));
     $user = json_decode($user);
     $username = $user->email;
     if(property_exists($user,'name')){
         $username = $user->name;
     }
     return view('auth0')->with('username',$username);
 }


 $email = $request->session()->get('email');
 if($auth->belong($email)){
     $user = $auth->userInfo($email);
     $user = json_decode($user);
     if($user->isAdmin){
         //this is an admin account
         $request->session()->put('isAdmin',true);
     }else{
         //this is a G suite account with access to see domain users but not an admin
         //dosn't matter because the difference is if the person is an admin or not
         $request->session()->put('isAdmin',false);
     }
 }else{
     //this is a normal account gmail or a non G suite account.
     $request->session()->put('isAdmin',false);
 }


?>