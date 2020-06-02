<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Response;

class Course extends Model
{
    //

    public $request;
    public function __construct($request=null){
        if($request!=null){
            $this->request=$request;
        }
    }
    //add teacher to a course only if admin
    public function addTeacher($courseId,$teacherEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $body = [
            'userId'=>$teacherEmail,
        ];
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , "https://classroom.googleapis.com/v1/courses/".$courseId."/teachers?key=".$api_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }



    //invite teacher to a course
    public function invTeacher($courseId,$teacherEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $body = [
            'userId'=>$teacherEmail,
            'role'=>'TEACHER',
            'courseId'=>$courseId
        ];
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , "https://classroom.googleapis.com/v1/invitations?key=".$api_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }



    //add student to a course only if admin
    public function addStudent($courseId,$studentEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $body = [
            'userId'=>$studentEmail,
        ];
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , "https://classroom.googleapis.com/v1/courses/".$courseId."/students?key=".$api_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }



    //invite student to a course
    public function invStudent($courseId,$studentEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $body = [
            'userId'=>$studentEmail,
            'role'=>'STUDENT',
            'courseId'=>$courseId
        ];
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , "https://classroom.googleapis.com/v1/invitations?key=".$api_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }

    public function createCourse($name,$section=null,$descriptionHeading=null,$description=null,$room=null,$ownerId=null,$courseState=null,$calendarId=null){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        $body = [
            'name'=>$name,
            'section'=>$section,
            'descriptionHeading'=>$descriptionHeading,
            'description'=>$description,
            'room'=>$room,
            'ownerId'=>$ownerId,
            'courseState'=>$courseState,
            'calendarId'=>$calendarId
        ];


        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , "https://classroom.googleapis.com/v1/courses?key=".$api_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }


    public function getCourses($courseState=null,$courseId=null){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        $url = "https://classroom.googleapis.com/v1/courses?key=".$api_key."&teacherId=me";

        // $url = $url."?id=".$courseId."&courseState=".$courseState."&key=".$api_key;
        if($courseId){
            $url = $url ."&id=".$courseId;
        }
        if($courseState){
            $url = $url."&courseStates=".$courseState;
        }

        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }

    public function getCourse($courseId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        $url = "https://classroom.googleapis.com/v1/courses/".$courseId."?key=".$api_key;

        // $url = $url."?id=".$courseId."&courseState=".$courseState."&key=".$api_key;


        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }

    public function getTeachers($id){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        $url = "https://classroom.googleapis.com/v1/courses/".$id."/teachers?key=".$api_key;

        // $url = $url."?id=".$courseId."&courseState=".$courseState."&key=".$api_key;


        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;

    }
    public function getStudents($id){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $this->request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        $url = "https://classroom.googleapis.com/v1/courses/".$id."/students?key=".$api_key;

        // $url = $url."?id=".$courseId."&courseState=".$courseState."&key=".$api_key;


        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }
}
