<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth;
use App\Course;
use App\CourseV2;
use App\Drive;
use App\DriveItem;
use App\UserActivity;
use App\Meet;
use App\MeetEvent;

class Test extends Controller
{
    //
    public function getTeacherMeetingData(Request $request){

        $fp = fopen('resources/emails.txt','r');
        $teacherList ="";
        while (($line = fgets($fp)) !== false) {
            $teacherList =$teacherList."".$line;
        }
        fclose($fp);
        $teacherList = explode(",",$teacherList);
        $data_csv = array(["Teacher_email","Event_name","Event_created_date","Event_start_time","Event_end_time","Event_organizer","Meeting_code","Meeting_participant_count","Meeting_email_list"]);
        $meet_api = new Meet();
        foreach($teacherList as $email){
           
            $calenderEvents = $meet_api->getMettingsAll($request,$email);
            $calendarCount = count($calenderEvents);
            foreach($calenderEvents as $event){
                if(property_exists($event,'conferenceData')){
                    if(property_exists($event->conferenceData,'conferenceSolution')){
                        if($event->conferenceData->conferenceSolution->name=="Google Meet"){
                            $data = [];

                            $data[] = $email;

                            if(property_exists($event,'summary')){
                                $data[] = $event->summary;
                            }else{
                                $data[] = "";
                            }
                            
                            if(property_exists($event,'created')){
                                $data[] = $event->created;
                            }else{
                                $data[] = "";
                            }
                            
                            if(property_exists($event,'start') && property_exists($event->start,'dateTime')){
                                $data[] = $event->start->dateTime;
                            }else{
                                $data[] = "";
                            }

                            if(property_exists($event,'end') && property_exists($event->end,'dateTime')){
                                $data[] = $event->end->dateTime;
                            }else{
                                $data[] = "";
                            }

                            if(property_exists($event,'organizer') && property_exists($event->organizer,'email')){
                                $data[] = $event->organizer->email;
                            }else{
                                $data[] = "";
                            }

                            if(property_exists($event,'conferenceData') && property_exists($event->conferenceData,'conferenceId')){
                                $data[] = $event->conferenceData->conferenceId;
                            }else{
                                $data[] = "";
                            }
                            

                            

                           
                            $temp_2 = 0;
                            $studentList = [];
                            $meetings = $meet_api->getAllMeetingRaport($request,str_replace("-","",$data[6]),$data[3],$data[4]);
                            foreach($meetings as $meeting){
                                if(property_exists($meeting,'events')){
                                    foreach($meeting->events as $events){
                                        if(property_exists($events,'parameters')){
                                            foreach($events->parameters as $parameter){
                                                if($parameter->name=="identifier"){
                                                    $presentEmail = $parameter->value;
                                                    if(!in_array($presentEmail,$studentList)){
                                                        $studentList[]= $presentEmail;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $temp_2=count($studentList);
                            }
                            $data[] = $temp_2;
                            if($temp_2>0){
                                $now_time = microtime();
                                $fff = fopen('resources/studentsList/'.$now_time.".txt",'w');
                                foreach($studentList as $studentEmail){
                                    fwrite($fff,$studentEmail."\n");
                                }
                                fclose($fff);
                                $data[] = $now_time.".txt";
                            }else{
                                $data[] = "0";
                            }

                            
                            $data_csv[] = $data;
                            
                        }
                    }
                }
            }
        }
        $file_name = time();
        $fp = fopen('resources/calendar/'.$file_name.'.csv', 'w'); 

        foreach($data_csv as $d){
            fputcsv($fp, $d);
        }

        fclose($fp); 
    }
    public function getMeetingData(Request $request){
        $course_api = new CourseV2();
        $response = $course_api->get_all_courses_data($request);
        $data_csv = array(["Course_id","Course_name","Event_name","Event_created_date","Event_start_time","Event_end_time","Event_organizer","Meeting_code","Meeting_participant_count","Meeting_email_list"]);
        for($i=0;$i<count($response);$i++){
            $course = $response[$i];

            // get the students count

            $calendarCount = 0;
            $meetingsCount = 0;
            $present = 0;
            $temp = 0;
            if(property_exists($course,'calendarId')){
                $meet_api = new Meet();
                $calenderEvents = $meet_api->getMettingsAll($request,$course->calendarId);
                $calendarCount = count($calenderEvents);
                foreach($calenderEvents as $event){
                    if(property_exists($event,'conferenceData')){
                        if(property_exists($event->conferenceData,'conferenceSolution')){
                            if($event->conferenceData->conferenceSolution->name=="Google Meet"){
                                $meetingsCount++;
                                $data = [];

                                $data[] = $course->id;
                                $data[] = $course->name;
                                if(property_exists($event,'summary')){
                                    $data[] = $event->summary;
                                }else{
                                    $data[] = "";
                                }
                                $data[] = $event->created;
                                $data[] = $event->start->dateTime;
                                $data[] = $event->end->dateTime;

                                $data[] = $event->organizer->email;
                                $data[] = $event->conferenceData->conferenceId;
                                $temp_2 = 0;
                                $studentList = [];
                                $meetings = $meet_api->getAllMeetingRaport($request,str_replace("-","",$event->conferenceData->conferenceId),$event->start->dateTime,$event->end->dateTime);
                                foreach($meetings as $meeting){
                                            if(property_exists($meeting,'events')){
                                                foreach($meeting->events as $events){
                                                    if(property_exists($events,'parameters')){
                                                        foreach($events->parameters as $parameter){
                                                            if($parameter->name=="identifier"){
                                                                $presentEmail = $parameter->value;
                                                                if(!in_array($presentEmail,$studentList)){
                                                                    $studentList[]= $presentEmail;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                        //     }
                                        // }
                                    }
                                    $temp_2=count($studentList);
                                    $temp = $temp + count($studentList);
                                }
                                $data[] = $temp_2;
                                $now_time = time();
                                $fff = fopen('resources/studentsList/'.$now_time.".txt",'w');
                                foreach($studentList as $studentEmail){
                                    fwrite($fff,$studentEmail."\n");
                                }
                                fclose($fff);
                                $data[] = $now_time.".txt";
                                $data_csv[] = $data;
                            }
                        }
                    }
                }
            }
            
            if(($i%600==0 && $i>0) || $i == (count($response) - 1)){

                $file_name = time();
                $fp = fopen('resources/calendar/'.$file_name.'.csv', 'w'); 
    
                foreach($data_csv as $d){
                    fputcsv($fp, $d);
                }

                fclose($fp); 
                $tmp = $data_csv[0];
                $data_csv =[];
                $data_csv[] = $tmp;
            }
        }   
    }

    public function refresh_access_token(Request $request){
        $auth = new Auth();

        //refresh_token
        $email = $request->session()->get('email');
        $refresh_code = $auth->getRefreshCode($email);
        if($refresh_code==null){
            $request->session()->flush();
        }
        $data = $auth->refreshAccessToken($email,$refresh_code);
        if($data!=null){
            $data = json_decode($data);
            $request->session()->put('id_token',$data->id_token);
            $request->session()->put('access_token',$data->access_token);
        }else{
            $auth->removeRefreshToken($email);
        }
    }

    public function get_all_classroom_v3(Request $request){
        $course_api = new CourseV2();
        $auth = new Auth();

        //refresh_token
        $email = $request->session()->get('email');
        $refresh_code = $auth->getRefreshCode($email);
        if($refresh_code==null){
            $request->session()->flush();
        }
        $data = $auth->refreshAccessToken($email,$refresh_code);
        if($data!=null){
            $data = json_decode($data);
            $request->session()->put('id_token',$data->id_token);
            $request->session()->put('access_token',$data->access_token);
        }else{
            $auth->removeRefreshToken($email);
        }


        $response = $course_api->get_all_courses_data($request);
        $data_csv = array(["Course_id","Course_name","Course_created_date","course_student_count","course_annoucenement_count","course_work_cours","course_work_cours_submition_percentage","course_calendar_events_count","course_calendar_meeting_count","averge_attendees_in_meeting"]);
        for($i=0;$i<count($response);$i++){
            $data = [];
            $course = $response[$i];

            // get the students count
            $data[] = $course->id;
            $data[] = $course->name;
            $data[] = $course->creationTime;

            $students = $course_api->getAllStudents($request,$course->id);
            $announcements = $course_api->getAllAnnouncements($request,$course->id);
            $courseWorks = $course_api->getAllCourseWorks($request,$course->id);

            
            $data[] = count($students);
            $data[] = count($announcements);
            $data[] = count($courseWorks);
            

            $submissions = 0;
            foreach($courseWorks as $courseWork){
                $studentSubmissions = $course_api->getAllCourseWorkSubmissions($request,$course->id,$courseWork->id);
                foreach($studentSubmissions as $submit){
                    if($submit->state=="TURNED_IN"){
                        $submissions++;
                    }
                }
            }
            $subRation = 0;
            if(count($students)>0 && count($courseWorks)>0){
                $subRation = ((($submissions/count($courseWorks))/count($students))*100);
            }

            $data[] = $subRation."%";



            $calendarCount = 0;
            $meetingsCount = 0;
            $present = 0;
            $temp = 0;
            if(property_exists($course,'calendarId')){
                $meet_api = new Meet();
                $calenderEvents = $meet_api->getMettingsAll($request,$course->calendarId);
                $calendarCount = count($calenderEvents);
                foreach($calenderEvents as $event){
                    if(property_exists($event,'conferenceData')){
                        if(property_exists($event->conferenceData,'conferenceSolution')){
                            if($event->conferenceData->conferenceSolution->name=="Google Meet"){
                                $meetingsCount++;
                                $meetings = $meet_api->getAllMeetingRaport($request,str_replace("-","",$event->conferenceData->conferenceId),$event->start->dateTime,$event->end->dateTime);
                                foreach($meetings as $meeting){
                                    $studentList = [];
                                            if(property_exists($meeting,'events')){
                                                foreach($meeting->events as $events){
                                                    if(property_exists($events,'parameters')){
                                                        foreach($events->parameters as $parameter){
                                                            if($parameter->name=="identifier"){
                                                                $presentEmail = $parameter->value;
                                                                if(!in_array($presentEmail,$studentList)){
                                                                    $studentList[]= $presentEmail;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                        //     }
                                        // }
                                    }
                                    $temp = $temp + count($studentList);
                                }
                            }
                        }
                    }
                }
            }
            $data[] = $calendarCount;
            $data[] = $meetingsCount;
            $meeting_ration = 0;
            if($temp>0 && $meetingsCount>0){
                $meeting_ration = $temp/$meetingsCount;
            }
            $data[] = $meeting_ration;
            $data_csv[] = $data;
            if(($i%100==0 && $i>0) || $i == (count($response) - 1)){

                //refresh_token
                $email = $request->session()->get('email');
                $refresh_code = $auth->getRefreshCode($email);
                if($refresh_code==null){
                    $request->session()->flush();
                }
                $data = $auth->refreshAccessToken($email,$refresh_code);
                if($data!=null){
                    $data = json_decode($data);
                    $request->session()->put('id_token',$data->id_token);
                    $request->session()->put('access_token',$data->access_token);
                }else{
                    $auth->removeRefreshToken($email);
                }

                $file_name = time();
                $fp = fopen('resources/class3/'.$file_name.'.csv', 'w'); 
    
                foreach($data_csv as $d){
                    fputcsv($fp, $d);
                }

                fclose($fp); 
                $tmp = $data_csv[0];
                $data_csv =[];
                $data_csv[] = $tmp;
            }
        }   
    }
    public function get_all_classroom_v2(Request $request){
        $course_api = new CourseV2();
        $response = $course_api->get_all_courses_data($request);
        $data_csv = array(["Course_id","Course_name","Course_created_date","course_student_count","course_annoucenement_count","course_work_cours","course_work_cours_submition_percentage","course_calendar_events_count","course_calendar_meeting_count","averge_attendees_in_meeting"]);
        for($i=0;$i<count($response);$i++){
            $data = [];
            $course = $response[$i];

            // get the students count
            $data[] = $course->id;
            $data[] = $course->name;
            $data[] = $course->creationTime;

            $students = $course_api->getAllStudents($request,$course->id);
            $announcements = $course_api->getAllAnnouncements($request,$course->id);
            $courseWorks = $course_api->getAllCourseWorks($request,$course->id);

            
            $data[] = count($students);
            $data[] = count($announcements);
            $data[] = count($courseWorks);
            

            $submissions = 0;
            foreach($courseWorks as $courseWork){
                $studentSubmissions = $course_api->getAllCourseWorkSubmissions($request,$course->id,$courseWork->id);
                foreach($studentSubmissions as $submit){
                    if($submit->state=="TURNED_IN"){
                        $submissions++;
                    }
                }
            }
            $subRation = 0;
            if(count($students)>0 && count($courseWorks)>0){
                $subRation = ((($submissions/count($courseWorks))/count($students))*100);
            }

            $data[] = $subRation."%";



            $calendarCount = 0;
            $meetingsCount = 0;
            $present = 0;
            $temp = 0;
            if(property_exists($course,'calendarId')){
                $meet_api = new Meet();
                $calenderEvents = $meet_api->getMettingsAll($request,$course->calendarId);
                $calendarCount = count($calenderEvents);
                foreach($calenderEvents as $event){
                    if(property_exists($event,'conferenceData')){
                        if(property_exists($event->conferenceData,'conferenceSolution')){
                            if($event->conferenceData->conferenceSolution->name=="Google Meet"){
                                $meetingsCount++;
                                $meetings = $meet_api->getAllMeetingRaport($request,str_replace("-","",$event->conferenceData->conferenceId),$event->start->dateTime,$event->end->dateTime);
                                foreach($meetings as $meeting){
                                    $studentList = [];
                                            if(property_exists($meeting,'events')){
                                                foreach($meeting->events as $events){
                                                    if(property_exists($events,'parameters')){
                                                        foreach($events->parameters as $parameter){
                                                            if($parameter->name=="identifier"){
                                                                $presentEmail = $parameter->value;
                                                                if(!in_array($presentEmail,$studentList)){
                                                                    $studentList[]= $presentEmail;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                        //     }
                                        // }
                                    }
                                    $temp = $temp + count($studentList);
                                }
                            }
                        }
                    }
                }
            }
            $data[] = $calendarCount;
            $data[] = $meetingsCount;
            $meeting_ration = 0;
            if($temp>0 && $meetingsCount>0){
                $meeting_ration = $temp/$meetingsCount;
            }
            $data[] = $meeting_ration;
            $data_csv[] = $data;
            if(($i%100==0 && $i>0) || $i == (count($response) - 1)){
                $file_name = time();
                $fp = fopen('resources/class2/'.$file_name.'.csv', 'w'); 
    
                foreach($data_csv as $d){
                    fputcsv($fp, $d);
                }
                fclose($fp); 
                $tmp = $data_csv[0];
                $data_csv =[];
                $data_csv[] = $tmp;

                //refresh_token
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
    }

    public function get_all_classroom(Request $request){
        $course_api = new CourseV2();
        $response = $course_api->get_all_courses_data($request);
        $data_csv = array(["Course_id","Course_name","Course_created_date","course_student_count","course_annoucenement_count","course_work_cours","course_work_cours_submition_percentage","course_calendar_events_count","course_calendar_meeting_count"]);
        foreach($response as $course){
            $data = [];
            

            // get the students count
            $data[] = $course->id;
            $data[] = $course->name;
            $data[] = $course->creationTime;

            $students = $course_api->getAllStudents($request,$course->id);
            $announcements = $course_api->getAllAnnouncements($request,$course->id);
            $courseWorks = $course_api->getAllCourseWorks($request,$course->id);

            
            $data[] = count($students);
            $data[] = count($announcements);
            $data[] = count($courseWorks);
            

            $submissions = 0;
            foreach($courseWorks as $courseWork){
                $studentSubmissions = $course_api->getAllCourseWorkSubmissions($request,$course->id,$courseWork->id);
                foreach($studentSubmissions as $submit){
                    if($submit->state=="TURNED_IN"){
                        $submissions++;
                    }
                }
            }
            $subRation = 0;
            if(count($students)>0 && count($courseWorks)>0){
                $subRation = ((($submissions/count($courseWorks))/count($students))*100);
            }

            $data[] = $subRation."%";



            $calendarCount = 0;
            $meetingsCount = 0;
            $present = 0;
            $temp = 0;
            if(property_exists($course,'calendarId')){
                $meet_api = new Meet();
                $calenderEvents = $meet_api->getMettingsAll($request,$course->calendarId);
                $calendarCount = count($calenderEvents);
                foreach($calenderEvents as $event){
                    if(property_exists($event,'conferenceData')){
                        if(property_exists($event->conferenceData,'conferenceSolution')){
                            if($event->conferenceData->conferenceSolution->name=="Google Meet"){
                                $meetingsCount++;
                            }
                        }
                    }
                }
            }
            $data[] = $calendarCount;
            $data[] = $meetingsCount;
            $data_csv[] = $data;
        }
        $fp = fopen('resources/classroom.csv', 'w'); 

        foreach($data_csv as $d){
            fputcsv($fp, $d);
        }
        fclose($fp); 
    }
    public function allStatics(Request $request){
        //step by step my boi

        $course_api = new CourseV2();
        // get all the courses for this user
        $all_courses_response = $course_api->getAllCourses($request);
        if($all_courses_response->responseCode==200){
            $all_courses = json_decode($all_courses_response->body);
            if(property_exists($all_courses,'courses')){
                //now i shall have all courses for this user in a object type
                $courses = $all_courses->courses;
                foreach($courses as $course){
                    echo "<h2>$course->id :: $course->name </h2>";
                    // get the students count
                    $students = $course_api->getAllStudents($request,$course->id);
                    $announcements = $course_api->getAllAnnouncements($request,$course->id);
                    $courseWorks = $course_api->getAllCourseWorks($request,$course->id);

                    echo "<h3>Classroom statistics : </h3>";
                    echo "<small>Students count :: ".count($students)."</small><br>";
                    echo "<small>Announcements count :: ".count($announcements)."</small><br>";
                    echo "<small>courseWorks count :: ".count($courseWorks)."</small><br>";

                    $submissions = 0;
                    foreach($courseWorks as $courseWork){
                        $studentSubmissions = $course_api->getAllCourseWorkSubmissions($request,$course->id,$courseWork->id);
                        foreach($studentSubmissions as $submit){
                            if($submit->state=="TURNED_IN"){
                                $submissions++;
                            }
                        }
                    }
                    $subRation = 0;
                    if(count($students)>0 && count($courseWorks)>0){
                        $subRation = ((($submissions/count($courseWorks))/count($students))*100);
                    }
                    echo "<small>courseWorks submition  ration :: ".$subRation."%</small><br>";
                    echo "<h3>Drive statistics : </h3>";
                    $drive_api = new Drive();
                    $drive_types = $drive_api->getAllDriveTypes($request,$course->teacherFolder->id);

                    echo "<small>Drive files count :: ".$drive_types->total."</small><br>";
                    echo "<small>images  :: ".$drive_types->images."</small><br>";
                    echo "<small>videos  :: ".$drive_types->videos."</small><br>";
                    echo "<small>documents  :: ".$drive_types->documents."</small><br>";
                    echo "<small>audios  :: ".$drive_types->audios."</small><br>";
                    echo "<small>others  :: ".$drive_types->others."</small><br>";

                    echo "<h3>Calendar statistics : </h3>";
                    $calendarCount = 0;
                    $meetingsCount = 0;
                    $present = 0;
                    $temp = 0;
                    if(property_exists($course,'calendarId')){
                        $meet_api = new Meet();
                        $calenderEvents = $meet_api->getMettingsAll($request,$course->calendarId);
                        $calendarCount = count($calenderEvents);
                        foreach($calenderEvents as $event){
                            if(property_exists($event,'conferenceData')){
                                if(property_exists($event->conferenceData,'conferenceSolution')){
                                    if($event->conferenceData->conferenceSolution->name=="Google Meet"){
                                        $meetingsCount++;
                                        $meetings = $meet_api->getAllMeetingRaport($request,str_replace("-","",$event->conferenceData->conferenceId),$event->start->dateTime,$event->end->dateTime);
                                        foreach($meetings as $meeting){
                                            $studentList = [];
                                                    if(property_exists($meeting,'events')){
                                                        foreach($meeting->events as $events){
                                                            if(property_exists($events,'parameters')){
                                                                foreach($events->parameters as $parameter){
                                                                    if($parameter->name=="identifier"){
                                                                        $presentEmail = $parameter->value;
                                                                        if(!in_array($presentEmail,$studentList)){
                                                                            $studentList[]= $presentEmail;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                //     }
                                                // }
                                            }
                                            $temp = $temp + count($studentList);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    echo "<small>Events Count  :: ".$calendarCount."</small><br>";
                    echo "<small>Meetings Count  :: ".$meetingsCount."</small><br>";
                    $meeting_ration = 0;
                    if($temp>0 && $meetingsCount>0){
                        $meeting_ration = $temp/$meetingsCount;
                    }
                    echo "<small>Meetings present Ratio  :: ".$meeting_ration."%</small><br>";
                }
            }
        }






        return "\n";
    }


    public function getMeetings(Request $request){
        $meet_api = new Meet();
        $events = $meet_api->getMeetings($request,"81418260314");
        $meet_api->getUserActivities($request,"81418260314","abdelkarim.chamlal@uae.ac.ma");


    }

    public function allDrive(Request $request){
        $course_api = new CourseV2();

        $courses_response = $course_api->getAllCourses($request);
        if($courses_response->responseCode==200){
            $courses = json_decode($courses_response->body);
            if(property_exists($courses,'courses')){
                foreach($courses->courses as $course){
                    $drive_api = new Drive();
                    $drive_api->getAllDriveStatics($request,$course->teacherFolder->id);
                    echo $course->name."<br>";
                    echo "<pre>";
                    var_dump($drive_api->driveObj);
                    echo "</pre>";
                    echo "<br><br>";
                }
            }
        }

    }
}
