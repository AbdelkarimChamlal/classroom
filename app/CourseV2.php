<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Response;

class CourseV2 extends Model
{
    //

    
    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    public function archiveClassroom($request,$courseId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $url = "https://classroom.googleapis.com/v1/courses/".$courseId."?updateMask=courseState&key=".$api_key;
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS,'{"courseState":"ARCHIVED"}');
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }


    public function deleteCourse($request,$courseId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        $url = "https://classroom.googleapis.com/v1/courses/".$courseId."?key=".$api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = new Response($httpCode,$result);
        return $response;
    }

    public function getUser($request,$userId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
      
        
        $url = "https://classroom.googleapis.com/v1/userProfiles/".$userId."?key=".$api_key;
        


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
    public function deleteStudent($request,$courseId,$studentId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        $url = "https://classroom.googleapis.com/v1/courses/".$courseId."/students/".$studentId."?key=".$api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $response = new Response($httpCode,$result);
        return $response;
    }

    public function deleteTeacher($request,$courseId,$teacherId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        $url = "https://classroom.googleapis.com/v1/courses/".$courseId."/teachers/".$teacherId."?key=".$api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $response = new Response($httpCode,$result);
        return $response;
    }


    public function get_all_courses_data($request){
        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $studentSubmissions_response = $this->get_all_courses_response($request,$nextPageToken);
            if($studentSubmissions_response->responseCode==200){
                $studentSubmissions_info = json_decode($studentSubmissions_response->body);
                if(property_exists($studentSubmissions_info,'courses')){
                    foreach($studentSubmissions_info->courses as $studentSubmissions){
                        $response[] = $studentSubmissions;
                    }
                    if(property_exists($studentSubmissions_info,'nextPageToken')){
                        $nextPageToken=$studentSubmissions_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{

                $state=false;
            }
        }
        return $response;
    }
    public function get_all_courses_response($request,$nextPageToken){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        if($nextPageToken!=null){
            $url = "https://classroom.googleapis.com/v1/courses?key=".$api_key."&teacherId=pedagogie.fstt@uae.ac.ma&pageToken=".$nextPageToken;
        }else{
            $url = "https://classroom.googleapis.com/v1/courses?key=".$api_key."&teacherId=pedagogie.fstt@uae.ac.ma";
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

    public function getAllCourseWorkSubmissions($request,$courseId,$workId){
        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $studentSubmissions_response = $this->getCourseWorkSubmissions($request,$courseId,$workId,$nextPageToken);
            if($studentSubmissions_response->responseCode==200){
                $studentSubmissions_info = json_decode($studentSubmissions_response->body);
                if(property_exists($studentSubmissions_info,'studentSubmissions')){
                    foreach($studentSubmissions_info->studentSubmissions as $studentSubmissions){
                        $response[] = $studentSubmissions;
                    }
                    if(property_exists($studentSubmissions_info,'nextPageToken')){
                        $nextPageToken=$studentSubmissions_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{

                $state=false;
            }
        }
        return $response;
    }
    public function getCourseWorkSubmissions($request,$id,$workId,$nextPageToken){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        if($nextPageToken!=null){
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/courseWork/".$workId."/studentSubmissions?pageToken=".$nextPageToken."&key=".$api_key;
        }else{
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/courseWork/".$workId."/studentSubmissions?key=".$api_key;
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

    public function getAllCourseWorks($request,$id){
        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $coursework_response = $this->getCourseWorks($request,$id,$nextPageToken);
            if($coursework_response->responseCode==200){
                $coursework_info = json_decode($coursework_response->body);
                if(property_exists($coursework_info,'courseWork')){
                    foreach($coursework_info->courseWork as $coursework){
                        $response[] = $coursework;
                    }
                    if(property_exists($coursework_info,'nextPageToken')){
                        $nextPageToken=$coursework_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{

                $state=false;
            }
        }
        return $response;
    }

    public function getCourseWorks($request,$id,$nextPageToken){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        if($nextPageToken!=null){
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/courseWork?pageToken=".$nextPageToken."&key=".$api_key;
        }else{
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/courseWork?key=".$api_key;
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
    public function getAllAnnouncements($request,$id){
        
        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $announcements_response = $this->getAnnouncements($request,$id,$nextPageToken);
            if($announcements_response->responseCode==200){
                $announcements_info = json_decode($announcements_response->body);
                if(property_exists($announcements_info,'announcements')){
                    foreach($announcements_info->announcements as $announcements){
                        $response[] = $announcements;
                    }
                    if(property_exists($announcements_info,'nextPageToken')){
                        $nextPageToken=$announcements_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{

                $state=false;
            }
        }
        return $response;
    }
    public function getAnnouncements($request,$id,$nextPageToken){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        if($nextPageToken!=null){
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/announcements?pageToken=".$nextPageToken."&key=".$api_key;
        }else{
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/announcements?key=".$api_key;
        }


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
    public function getAllStudents($request,$id){

        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $students_response = $this->getStudents($request,$id,$nextPageToken);
            if($students_response->responseCode==200){
                $students_info = json_decode($students_response->body);
                if(property_exists($students_info,'students')){
                    foreach($students_info->students as $student){
                        $response[] = $student;
                    }
                    if(property_exists($students_info,'nextPageToken')){
                        $nextPageToken=$students_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{
                $state=false;
            }
        }
        return $response;
    }

    public function updateCourse($request,$id,$data){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $url = "https://classroom.googleapis.com/v1/courses/".$id."?key=".$api_key;
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json','Authorization: Bearer '.$access_code));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }


    public function getTeachers($request,$id){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
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

    public function getStudents($request,$id,$nextPageToken=null){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        if($nextPageToken!=null){
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/students?pageToken=".$nextPageToken."&key=".$api_key;
        }else{
            $url = "https://classroom.googleapis.com/v1/courses/".$id."/students?key=".$api_key;
        }


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


    public function getCourse($request,$courseId){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        $url = "https://classroom.googleapis.com/v1/courses/".$courseId."?key=".$api_key;

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

    public function getAllCourses($request){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token

        // $url = "https://classroom.googleapis.com/v1/courses?key=".$api_key;

        $url = "https://classroom.googleapis.com/v1/courses?key=".$api_key."&teacherId=me";
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

    public function getCoursesWithStats($request,$stats){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $url = "https://classroom.googleapis.com/v1/courses?key=".$api_key."&teacherId=me&courseStates=".$stats;
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

    public function createCourse($request,$name,$section=null,$descriptionHeading=null,$description=null,$room=null,$ownerId=null,$courseState=null,$calendarId=null){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
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
      //add teacher to a course only if admin
      public function addTeacher($request,$courseId,$teacherEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
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
    public function invTeacher($request,$courseId,$teacherEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
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
    public function addStudent($request,$courseId,$studentEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
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
    public function invStudent($request,$courseId,$studentEmail){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
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

}
