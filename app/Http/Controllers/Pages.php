<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth;
use App\Course;
use App\Drive;
use App\DriveItem;
use App\UserActivity;
use App\CourseV2;

class Pages extends Controller
{
    //

    public function db_test(Request $request){
        $values = CourseV2::distinct()->get(['semester']);
        for($i=0;$i<count($values);$i++){
            echo $values[$i]['semester'];
        }
        // echo "<pre>";
        // var_dump($values);
        // echo"</pre>";
    }
    public function main(Request $request){
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



        return view('pages.main');
    }


    public function createCourse(Request $request){
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

        ////////////////////////////////////////////////////////////////////////////////////////////
        $this->validate($request,[
            'course_name'=>'required'
        ]);
        $course_name = $request->input('course_name');
        $select = $request->input('course_select');
        $descriptionHeading=$request->input('course_descriptionHeading');
        $description = $request->input('course_description');
        $room=$request->input('course_room');
        $calendarId=$request->input('course_calendarId');
        $courseState = $request->input('courseState');

        $course = new Course($request);
        $response = $course->createCourse($course_name,$select,$descriptionHeading,$description,$room,'me',$courseState,$calendarId);
        if($response->responseCode==200){
            $course_info = json_decode($response->body);
            $courseId = $course_info->id;
            $teachers = $request->input('teacher_email');
            if($teachers!=null){
                $teachers = explode(',',$teachers);
                for($i=0;$i<count($teachers);$i++){
                    if($request->session()->get('isAdmin')){
                        $course->addTeacher($courseId,$teachers[$i]);
                    }else{
                        $course->invTeacher($courseId,$teachers[$i]);
                    }
                }
            }
            $students = $request->input('list');
            if($students!=null){
                $students = explode("\n",$students);
                for($i=0;$i<count($students);$i++){
                    if($request->session()->get('isAdmin')){
                        $course->addStudent($courseId,$students[$i]);
                    }else{
                        $course->invStudent($courseId,$students[$i]);
                    }
                }
            }

            
        }else{
            echo $response->body;
        }

        return redirect('/courses');

    }


    public function show(Request $request,$stats=null){
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

      ////////////////////////////////////////////////////////////////////////////////////////////

      $course = new Course($request);
      $response;
      if($stats){
        $response = $course->getCourses($stats);
      }else{
        $response = $course->getCourses();
      }

      if($response->responseCode==200){
          $courses = json_decode($response->body);
          if(property_exists($courses,'courses')){
            $courses = $courses->courses;
          }else{
            $courses = array();
          }
          
          return view('pages.courses')->with('courses',$courses);
      }
      
      
      return $response->body."  ".$stats;
    }
    public function coursesIndex(Request $request){
        $stats = $request->stats;
        if($stats=="all"){
            return redirect('/courses');
        }else if($stats=="ACTIVE"){
            return redirect('/courses/ACTIVE');
        }else if($stats=="ARCHIVED"){
            return redirect('/courses/ARCHIVED');
        }else if($stats=="PROVISIONED"){
            return redirect('/courses/PROVISIONED');
        }

        return redirect('/courses');
    }

    public function create(Request $request){
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

      ////////////////////////////////////////////////////////////////////////////////////////////

        return view('pages.create');
    }



    public function createMass(Request $request){
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

      ////////////////////////////////////////////////////////////////////////////////////////////

        return view('pages.createMass');
    }

    public function executeCreationOnMass(Request $request){
        $this->validate($request,[
            'file'=>'required',
        ]);
        $file = $request->file('file');
        $fileName = time().'.'.$file->getClientOriginalExtension();  

        $file->move('resources', $fileName);

        $fp = fopen('resources/'.$fileName, "r");


        $courses = [];

        while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) 
        {
          // Each individual array is being pushed into the nested array
          $courses[] = $data;		
        }

        fclose($fp);

        for($i=1;$i<count($courses);$i++){
            $course = $courses[$i];
            $course_name = $course[0];
            $select = $course[1];
            $descriptionHeading=$course[2];
            $description = $course[3];
            $room=$course[4];
            $calendarId=$course[6];
            $courseState = $course[5];
            $course_api = new Course($request);

            $response = $course_api->createCourse($course_name,$select,$descriptionHeading,$description,$room,'me',$courseState,$calendarId);
            //the course created succefully
            if($response->responseCode==200){
                echo "<pre>";
                var_dump($response->body);
                echo "</pre>";
                $course_info = json_decode($response->body);
                $courseId = $course_info->id;
                $teachers = $course[7];
                //adding teachers
                if($teachers!=null){
                    $teachers = explode(':',$teachers);
                    if($request->session()->get('isAdmin')){
                        for($j=0;$j<count($teachers);$j++)
                            $course_api->addTeacher($courseId,$teachers[$j]);
                    }else{
                        for($j=0;$j<count($teachers);$j++)
                            $course_api->invTeacher($courseId,$teachers[$j]);
                    }
                }
                //adding students
                $students_file = $course[8].".txt";
                $students = fopen('resources/'.$students_file,'r');

                $list = [];
                while(!feof($students)){
                    //store the students in an array
                    $list[] = fgets($students);
                }
                fclose($students);
                if($list!=null && count($list)>0){
                    for($j=0;$j<count($list);$j++){
                        if($request->session()->get('isAdmin')){
                            $course_api->addStudent($courseId,$list[$j]);
                        }else{
                            $course_api->invStudent($courseId,$list[$j]);
                        }
                    }
                }
            }
        }
       
        return "";
    }

    public function showReport(Request $request,$id){
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

      ////////////////////////////////////////////////////////////////////////////////////////////

      $course_api = new Course($request);
      $course = $course_api->getCourse($id);
      $teachers = $course_api->getTeachers($id);
      $students = $course_api->getStudents($id);
      echo "<pre>";
      echo "Course information : ";
      echo var_dump($course->body);
      echo "Teachers :";
      echo var_dump($teachers->body);
      echo "Students : ";
      echo var_dump($students->body);
      echo"</pre>";
      return;
    }

    ////////////////////////////////////////////////////////////
    // testing airea
    public function access_token(Request $request){
        return $request->session()->get('access_token');
    }

    public function get_all_folder_items(Request $request){
        $drive_api = new Drive();
        
        $drive_api->getAllItemsInAFolder($request,"0BzO0PXNjzWn3UGFaWlVvU1JxR28","/");
        $result =$drive_api->list;
        echo "<pre>";
        echo var_dump($result);
        echo "</pre>";
    }
    
    public function get_user_acitivity(Request $request){
        $drive_api = new Drive();
        $response = $drive_api->getUserActivity($request,"test-dev-2020@uae.ac.ma");
        $user = json_decode($response->body);
        echo "<pre>";
        $allActivities = [];
        for ($i=0;$i<count($user->items);$i++){
           $date = date("Y-m-d ha",strtotime($user->items[$i]->id->time));
           for($j=0;$j<count($user->items[$i]->events);$j++){
               if($user->items[$i]->events[$j]->type=="access"){
                    $activity = new UserActivity();
                    $activity->event_date = $date;
                    $activity->event_name= $user->items[$i]->events[$j]->name;
                    //this is not gonna be perfect but i will do it because am too lazy
                    //but i mean no one said not to come back here and do something better.
                    for($h=0;$h<count($user->items[$i]->events[$j]->parameters);$h++){
                        if($user->items[$i]->events[$j]->parameters[$h]->name=="doc_id"){
                            $activity->doc_id = $user->items[$i]->events[$j]->parameters[$h]->value;

                        }else if($user->items[$i]->events[$j]->parameters[$h]->name=="doc_type"){
                            $activity->doc_type = $user->items[$i]->events[$j]->parameters[$h]->value;

                        }else if($user->items[$i]->events[$j]->parameters[$h]->name=="doc_title"){
                            $activity->doc_title = $user->items[$i]->events[$j]->parameters[$h]->value;
                        }
                    }
                    $allActivities[]=$activity; 
               }
           }
       }
        echo var_dump($allActivities);
        echo "</pre>"; 
    }

    ////////////////////////////////////////////////////////////

    public function getUserActivitiesInAClassroom(Request $request){
        $drive_api = new Drive();
        echo "<pre>";

        $list = $drive_api->getUserAcitivitiesInClassroom($request,"test-dev-2020@uae.ac.ma","81418260314");
        echo var_dump($list);
        echo "</pre>";
    
    }
    ////////////////////////////////////////////////////////////


    public function signOut(Request $request){
        $request->session()->flush();
        return view('signout');
        // return redirect('/');

    }
}
