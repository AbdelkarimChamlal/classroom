<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Auth;
use App\Course;
use App\Drive;
use App\DriveItem;
use App\UserActivity;
use App\Meet;
use App\Meeting;
use App\CourseV2;
use App\DataObj;
use ZipArchive;

class Statics extends Controller
{
    //
    public function exportConfirmed(Request $request){
        
        $this->validate($request,[
            'filter'=>'required',
        ]);

        $course_api = new CourseV2();
        $courses_response;
        if($request->has('stats') && $request->input('stats')!=""){
            $courses_response = $course_api->getCoursesWithStats($request,$request->input('stats'));
        }else{
            $courses_response = $course_api->getAllCourses($request);
        }


        $multiCondition = 0;
        $database_courses = CourseV2::where('id','!=','1');
        if($request->has('establishment') && $request->input('establishment')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('establishment',$request->input('establishment'));
        }
        if($request->has('diploma') && $request->input('diploma')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('diploma',$request->input('diploma'));
        }
        if($request->has('filiere') && $request->input('filiere')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('filiere',$request->input('filiere'));
        }
        if($request->has('semester') && $request->input('semester')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('semester',$request->input('semester'));
        }

        $database_courses = $database_courses->get();
        $courses = [];

        // here i could do a loop in the all courses to extract courses ids to make things simple
        // should come here later
        // but for now i just want it to work
        
        if($database_courses!=null){
            if($courses_response->responseCode==200){
                $courses_info = json_decode($courses_response->body);
                if(property_exists($courses_info,'courses')){
                    $allCourses = $courses_info->courses;
                        for($i=0 ; $i<count($database_courses) ;$i++){
                            for($j=0; $j<count($allCourses); $j++){
                                if($allCourses[$j]->id == $database_courses[$i]->id){
                                    $courses[] = $allCourses[$j];
                                break;
                                }
                            }
                        }
                        if($multiCondition == 0){
                            $courses = $allCourses;
                        }
                }
            }
        }
        $establishment = CourseV2::distinct()->get(['establishment']);
        $diploma = CourseV2::distinct()->get(['diploma']);
        $filiere = CourseV2::distinct()->get(['filiere']);
        $semester = CourseV2::distinct()->get(['semester']);

        $data = array(
            'courses'=>$courses,
            'establishment'=>$establishment,
            'diploma'=>$diploma,
            'semester'=>$semester,
            'filiere'=>$filiere
        );

        return view('statics.export')->with($data);
    }

    public function exportCourses(Request $request){
        $this->validate($request,[
            'exportChoice'=>'required',
            'export'=>'required',
            'selected'=>'required'
        ]);

        $course_api = new CourseV2();
        if($request->input('exportChoice')=='classroom'){
            $selected = $request->input('selected');
            //classroom id
            //classroom name
            //created time
            //students count
            //announcements count
            //courseWorks count
            //courseWork submition rate

            $data_csv = array(["id","name","created time","students count","announcements count","courseWorks count","submition rate"]);

            foreach($selected as $courseId){
                $course_response = $course_api->getCourse($request,$courseId);
                if($course_response->responseCode==200){
                    $course = json_decode($course_response->body);

                    $data = [];

                    $data[] = $courseId;
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

                    $data_csv[] = $data;
                }
            }
            $fileName = microtime();
            $fp = fopen('resources/'.$fileName.'.csv', 'w'); 
            foreach($data_csv as $d){
                fputcsv($fp, $d);
            }
            fclose($fp); 

            $file= public_path().'/resources/'.$fileName.'.csv';
            $headers = array(
                'Content-Type: application/force-download',
              );
            return response()->download($file, 'classroom_statics.csv', $headers);



        }else
        if($request->input('exportChoice')=='drive'){

            $selected = $request->input('selected');
            $data_csv = array(["id","name","created time","total files","videos","audios","images","documents","others"]);
            foreach($selected as $courseId){
                $course_response = $course_api->getCourse($request,$courseId);
                if($course_response->responseCode==200){
                    $course = json_decode($course_response->body);

                    $data = [];

                    $data[] = $courseId;
                    $data[] = $course->name;
                    $data[] = $course->creationTime;

                    $drive_api = new Drive();
                    $drive_types = $drive_api->getAllDriveTypes($request,$course->teacherFolder->id);

                    $data[] = $drive_types->total;
                    $data[] =$drive_types->videos ;
                    $data[] = $drive_types->audios;
                    $data[] =$drive_types->images ;
                    $data[] = $drive_types->documents;
                    $data[] = $drive_types->others;
                    $data_csv[] = $data;
                }

            }
            $fileName = microtime();
            $fp = fopen('resources/'.$fileName.'.csv', 'w'); 
            foreach($data_csv as $d){
                fputcsv($fp, $d);
            }
            fclose($fp); 

            $file= public_path().'/resources/'.$fileName.'.csv';
            $headers = array(
                'Content-Type: application/force-download',
              );
            return response()->download($file, 'drive_statics.csv', $headers);


        }else 
        if($request->input('exportChoice')=='calendar'){

            $listIndex = 0;
            $listsmap =[];
            $selected = $request->input('selected');
            $data_csv = array(["id","name","Event name","Event start","Event end","Event Type","participants count","participants list"]);
            $meet_api= new Meet();
            foreach($selected as $courseId){

                $course_response = $course_api->getCourse($request,$courseId);

                if($course_response->responseCode==200){

                    $course = json_decode($course_response->body);
                    if(property_exists($course,'calendarId')){
                        $calenderEvents = $meet_api->getMettingsAll($request,$course->calendarId);
                        $calendarCount = count($calenderEvents);

                        if($calendarCount>0){
                            foreach($calenderEvents as $event){
                                $data = [];

                                $data[] = $courseId;

                                $data[] = $course->name;

                                if(property_exists($event,'summary')){
                                    $data[] = $event->summary;
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


                                if(property_exists($event,'conferenceData') && property_exists($event->conferenceData,'conferenceSolution') && $event->conferenceData->conferenceSolution->name=="Google Meet"){

                                    $data[] = "Meeting";

                                    $studentList = [];

                                    $meetings = $meet_api->getAllMeetingRaport($request,str_replace("-","",$event->conferenceData->conferenceId),$data[3],$data[4]);
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
                                    }

                                    $data[] = count($studentList);

                                    if(count($studentList)>0){
                                        $now_time = microtime();
                                        $fff = fopen('resources/studentsList/'.$now_time.".txt",'w');
                                        foreach($studentList as $studentEmail){
                                            fwrite($fff,$studentEmail."\n");
                                        }
                                        fclose($fff);
                                        $data[] = "list".$listIndex.".txt";
                                        $listIndex++;
                                        $listsmap[$data[7]] = 'resources/studentsList/'.$now_time.".txt";
                                    }else{
                                        $data[] = "";
                                    }
                                        
                                }else{
                                    $data[] = "deadline";
                                    $data[] = "";
                                    $data[] = "";
                                }
                                $data_csv[] = $data;
                            }
                        }
                    }
                }
            }


            $fileName = microtime();
            $fp = fopen('resources/'.$fileName.'.csv', 'w'); 
            foreach($data_csv as $d){
                fputcsv($fp, $d);
            }
            fclose($fp);




            $zip = new ZipArchive;
            $zipFile = 'resources/'.microtime() .".zip";
            if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE){

                $zip->addFile('resources/'.$fileName.'.csv', 'calendar_statics.csv');
            
                    for($i=1;$i<count($data_csv);$i++){
                        if($data_csv[$i][6]>0){
                            $zip->addFile($listsmap[$data_csv[$i][7]],'studentList/'.$data_csv[$i][7]);
                        }
                    }
            
                // All files are added, so close the zip file.
                $zip->close();
            }
            $file= public_path().'/'.$zipFile;
            $headers = array(
                'Content-Type: application/force-download',
              );
            return response()->download($file, 'calendar_statics.zip', $headers);

        }else{
            return redirect('/');
        }
    }

    public function export(Request $request){
        $course_api = new CourseV2();
        $courses_response = $course_api->getAllCourses($request);
        if($courses_response->responseCode==200){
            $courses = json_decode($courses_response->body);
            if(property_exists($courses,'courses')){
                $courses = $courses->courses;
            }else{
                $courses = array();
            }
            $establishment = CourseV2::distinct()->get(['establishment']);
            $diploma = CourseV2::distinct()->get(['diploma']);
            $filiere = CourseV2::distinct()->get(['filiere']);
            $semester = CourseV2::distinct()->get(['semester']);

            $data = array(
                'courses'=>$courses,
                'establishment'=>$establishment,
                'diploma'=>$diploma,
                'semester'=>$semester,
                'filiere'=>$filiere
            );

            return view('statics.export')->with($data);
        }
        return redirect('/');
    }


    public function allGeneralStaticsFilter(Request  $request){
        
              ///////////////////////////////////////////the auth part //////////////////////////////////////////
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
      
            ///////////////////////////////////////////is the user an admin //////////////////////////////////////
              $email = $request->session()->get('email');
              if($auth->belong($email)){
                    $user = $auth->userInfo($email);
                    $user = json_decode($user);
                    if($user->isAdmin){
                        $request->session()->put('isAdmin',true);
                    }else{
                        $request->session()->put('isAdmin',false);
                    }
                  
              }else{
                  $request->session()->put('isAdmin',false);
              }
            //////////////////////////////////////////auth part end/////////////////////////////////////////////
      
        $this->validate($request,[
            'filter'=>'required',
        ]);
        $course_api = new CourseV2();
        $courses_response;
        if($request->has('stats') && $request->input('stats')!=""){
            $courses_response = $course_api->getCoursesWithStats($request,$request->input('stats'));
        }else{
            $courses_response = $course_api->getAllCourses($request);
        }


        $multiCondition = 0;
        $database_courses = CourseV2::where('establishment','!=','karimmm');
        if($request->has('establishment') && $request->input('establishment')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('establishment',$request->input('establishment'));
        }
        if($request->has('diploma') && $request->input('diploma')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('diploma',$request->input('diploma'));
        }
        if($request->has('filiere') && $request->input('filiere')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('filiere',$request->input('filiere'));
        }
        if($request->has('semester') && $request->input('semester')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('semester',$request->input('semester'));
        }

        $database_courses = $database_courses->get();
        $courses = [];

        // here i could do a loop in the all courses to extract courses ids to make things simple
        // should come here later
        // but for now i just want it to work
        
        if($database_courses!=null){
            if($courses_response->responseCode==200){
                $courses_info = json_decode($courses_response->body);
                if(property_exists($courses_info,'courses')){
                    $allCourses = $courses_info->courses;
                        foreach($database_courses as $course_db){
                            for($j=0; $j<count($allCourses); $j++){
                                if($allCourses[$j]->id == $course_db->id){
                                    $courses[] = $allCourses[$j];
                                break;
                                }
                            }
                        }
                        if($multiCondition == 0){
                            $courses = $allCourses;
                        }
                }
            }
        }
        $establishment = CourseV2::distinct()->get(['establishment']);
        $diploma = CourseV2::distinct()->get(['diploma']);
        $filiere = CourseV2::distinct()->get(['filiere']);
        $semester = CourseV2::distinct()->get(['semester']);

        $data = array(
            'courses'=>$courses,
            'establishment'=>$establishment,
            'diploma'=>$diploma,
            'semester'=>$semester,
            'filiere'=>$filiere
        );

        return view('statics.allStatics')->with($data);
    }
    public function allGeneralStatics(Request $request){
        ///////////////////////////////////////////the auth part //////////////////////////////////////////
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

      ///////////////////////////////////////////is the user an admin //////////////////////////////////////
        $email = $request->session()->get('email');
        if($auth->belong($email)){
            $user = $auth->userInfo($email);
            $user = json_decode($user);
            if($user->isAdmin){
                $request->session()->put('isAdmin',true);
            }else{
                $request->session()->put('isAdmin',false);
            }
            
        }else{
            $request->session()->put('isAdmin',false);
        }
      //////////////////////////////////////////auth part end/////////////////////////////////////////////
      $course_api = new CourseV2();
      $courses_response = $course_api->getAllCourses($request);
      if($courses_response->responseCode==200){
          $courses = json_decode($courses_response->body);
          if(property_exists($courses,'courses')){
              $courses = $courses->courses;
          }else{
              $courses = array();
          }
          $establishment = CourseV2::distinct()->get(['establishment']);
          $diploma = CourseV2::distinct()->get(['diploma']);
          $filiere = CourseV2::distinct()->get(['filiere']);
          $semester = CourseV2::distinct()->get(['semester']);
  
          $data = array(
              'courses'=>$courses,
              'establishment'=>$establishment,
              'diploma'=>$diploma,
              'semester'=>$semester,
              'filiere'=>$filiere
          );
  


        return view('statics.allStatics')->with($data);
      }
    }

    public function generalStatics(Request $request,$courseId){
        ///////////////////////////////////////////the auth part //////////////////////////////////////////
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

      ///////////////////////////////////////////is the user an admin //////////////////////////////////////
        $email = $request->session()->get('email');
        if($auth->belong($email)){
            $user = $auth->userInfo($email);
            $user = json_decode($user);
            if($user->isAdmin){
                $request->session()->put('isAdmin',true);
            }else{
                $request->session()->put('isAdmin',false);
            }
            
        }else{
            $request->session()->put('isAdmin',false);
        }
      //////////////////////////////////////////auth part end/////////////////////////////////////////////
        $course_api = new CourseV2();
        $course_response = $course_api->getCourse($request,$courseId);
        if($course_response->responseCode==200){
            $course = json_decode($course_response->body);

            // get the students count
            $students = $course_api->getAllStudents($request,$course->id);
            $announcements = $course_api->getAllAnnouncements($request,$course->id);
            $courseWorks = $course_api->getAllCourseWorks($request,$course->id);


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
            
            $drive_api = new Drive();
            $drive_types = $drive_api->getAllDriveTypes($request,$course->teacherFolder->id);

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

            $meeting_ration = 0;

            if($temp>0 && $meetingsCount>0){
                $meeting_ration = $temp/$meetingsCount;
            }

            $classroom = new DataObj();
            $classroom->name = $course->name;
            $classroom->studentCount = count($students);
            $classroom->announcementCount = count($announcements);
            $classroom->courseWorkCount = count($courseWorks);
            $classroom->submitRatio = $subRation;

            $drive = new DataObj();
            $drive->total = $drive_types->total;
            $drive->images =$drive_types->images ;
            $drive->videos =$drive_types->videos ;
            $drive->documents = $drive_types->documents;
            $drive->audios = $drive_types->audios;
            $drive->others = $drive_types->others;
            
            $calendar = new DataObj();
            $calendar->eventsCount = $calendarCount;
            $calendar->meetingCount = $meetingsCount;
            $calendar->meetingPresentRatio = $meeting_ration;


            $data = array(
                'id'=>$courseId,
                'classroom'=>$classroom,
                'drive'=>$drive,
                'calendar'=>$calendar
            );
    
            return view('statics.generalStatics')->with($data);
        }
    }






    public function my_courses(Request $request,$stats=null){
        ///////////////////////////////////////////the auth part //////////////////////////////////////////
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

      ///////////////////////////////////////////is the user an admin //////////////////////////////////////
        $email = $request->session()->get('email');
        if($auth->belong($email)){
            $user = $auth->userInfo($email);
            $user = json_decode($user);
            if($user->isAdmin){
                $request->session()->put('isAdmin',true);
            }else{
                $request->session()->put('isAdmin',false);
            }
            
        }else{
            $request->session()->put('isAdmin',false);
        }
      //////////////////////////////////////////auth part end/////////////////////////////////////////////
        $course_api = new CourseV2();
        $courses_response = $course_api->getAllCourses($request);
        if($courses_response->responseCode==200){
            $courses = json_decode($courses_response->body);
            if(property_exists($courses,'courses')){
                $courses = $courses->courses;
            }else{
                $courses = array();
            }
            $establishment = CourseV2::distinct()->get(['establishment']);
            $diploma = CourseV2::distinct()->get(['diploma']);
            $filiere = CourseV2::distinct()->get(['filiere']);
            $semester = CourseV2::distinct()->get(['semester']);
    
            $data = array(
                'courses'=>$courses,
                'establishment'=>$establishment,
                'diploma'=>$diploma,
                'semester'=>$semester,
                'filiere'=>$filiere
            );
    
            return view('statics.courses')->with($data);
        }
        return redirect('/');
        // //the auth part 
        // $auth = new Auth($request);
        // if(!$auth->isSigned()){
        //     return view('sign');
        // }
        // if(!$auth->hasAccess()){
        //     $user = $auth->getUserInformation($request->session()->get('id_token'));
        //     $user = json_decode($user);
        //     $username = $user->email;
        //     if(property_exists($user,'name')){
        //         $username = $user->name;
        //     }
        //     return view('auth0')->with('username',$username);
        // }
        // $email = $request->session()->get('email');
        // if($auth->belong($email)){
        //     $user = $auth->userInfo($email);
        //     $user = json_decode($user);
        //     if($user->isAdmin){
        //         //this is an admin account
        //         $request->session()->put('isAdmin',true);
        //     }else{
        //         //this is a G suite account with access to see domain users but not an admin
        //         //dosn't matter because the difference is if the person is an admin or not
        //         $request->session()->put('isAdmin',false);
        //     }
        // }else{
        //     //this is a normal account gmail or a non G suite account.
        //     $request->session()->put('isAdmin',false);
        // }

        // ////////////////////////////////////////////////////////////////////////////////////////////

        // $course = new Course($request);
        // $response;
        // if($stats){
        //     $response = $course->getCourses($stats);
        // }else{
        //     $response = $course->getCourses();
        // }

        // if($response->responseCode==200){
        //     $courses = json_decode($response->body);
        //     if(property_exists($courses,'courses')){
        //         $courses = $courses->courses;
        //     }else{
        //         $courses = array();
        //     }
        //     return view('statics.courses')->with('courses',$courses);
        //     }
        // return $response->body."  ".$stats;
    }

    public function coursesIndex(Request $request){
        
              ///////////////////////////////////////////the auth part //////////////////////////////////////////
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
      
            ///////////////////////////////////////////is the user an admin //////////////////////////////////////
              $email = $request->session()->get('email');
              if($auth->belong($email)){
                    $user = $auth->userInfo($email);
                    $user = json_decode($user);
                    if($user->isAdmin){
                        $request->session()->put('isAdmin',true);
                    }else{
                        $request->session()->put('isAdmin',false);
                    }
                  
              }else{
                  $request->session()->put('isAdmin',false);
              }
            //////////////////////////////////////////auth part end/////////////////////////////////////////////
      
        $this->validate($request,[
            'filter'=>'required',
        ]);
        $course_api = new CourseV2();
        $courses_response;
        if($request->has('stats') && $request->input('stats')!=""){
            $courses_response = $course_api->getCoursesWithStats($request,$request->input('stats'));
        }else{
            $courses_response = $course_api->getAllCourses($request);
        }


        $multiCondition = 0;
        $database_courses = CourseV2::where('establishment','!=','karimmm');
        if($request->has('establishment') && $request->input('establishment')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('establishment',$request->input('establishment'));
        }
        if($request->has('diploma') && $request->input('diploma')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('diploma',$request->input('diploma'));
        }
        if($request->has('filiere') && $request->input('filiere')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('filiere',$request->input('filiere'));
        }
        if($request->has('semester') && $request->input('semester')!=""){
            $multiCondition = 1;
            $database_courses = $database_courses->where('semester',$request->input('semester'));
        }

        $database_courses = $database_courses->get();
        $courses = [];

        // here i could do a loop in the all courses to extract courses ids to make things simple
        // should come here later
        // but for now i just want it to work
        
        if($database_courses!=null){
            if($courses_response->responseCode==200){
                $courses_info = json_decode($courses_response->body);
                if(property_exists($courses_info,'courses')){
                    $allCourses = $courses_info->courses;
                        foreach($database_courses as $course_db){
                            for($j=0; $j<count($allCourses); $j++){
                                if($allCourses[$j]->id == $course_db->id){
                                    $courses[] = $allCourses[$j];
                                break;
                                }
                            }
                        }
                        if($multiCondition == 0){
                            $courses = $allCourses;
                        }
                }
            }
        }
        $establishment = CourseV2::distinct()->get(['establishment']);
        $diploma = CourseV2::distinct()->get(['diploma']);
        $filiere = CourseV2::distinct()->get(['filiere']);
        $semester = CourseV2::distinct()->get(['semester']);

        $data = array(
            'courses'=>$courses,
            'establishment'=>$establishment,
            'diploma'=>$diploma,
            'semester'=>$semester,
            'filiere'=>$filiere
        );

        return view('statics.courses')->with($data);
    }

    public function course(Request $request,$courseId){
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
        /////////////////////////////////////////////////////////////////////////
        $course_api = new CourseV2();
        $course_response = $course_api->getCourse($request,$courseId);
        $students_response = $course_api->getStudents($request,$courseId);
        if($students_response->responseCode==200 && $course_response->responseCode==200){
            $students = json_decode($students_response->body);
            $students_emails=[];
            $course=json_decode($course_response->body);
            if(property_exists($students,'students')){
                for($i=0;$i<count($students->students);$i++){
                    $students_emails[]=$students->students[$i]->profile->emailAddress;
                }
            }
            $data = array(
                'emails' => $students_emails,
                'course' => $course
            );
            return view('statics.course')->with($data);
        }else{
            
            return redirect('/statics');
        }
    }

    public function driveStatics(Request $request,$courseId,$student_email){
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
        /////////////////////////////////////////////////////////////////////////
        $drive_api = new Drive();
        $activities = $drive_api->getUserAcitivitiesInClassroom($request,$student_email,$courseId);
        $course_api = new Course($request);
        $course_response = $course_api->getCourse($courseId);
        if($course_response->responseCode==200){
            $course = json_decode($course_response->body);
            $data = array(
                'course'=>$course,
                'email'=>$student_email,
                'activities'=> $activities
            );
            return view('statics.drive')->with($data);
        }
        return redirect('/statics');
    }


    public function meetStatics(Request $request,$courseId,$student_email){
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
        /////////////////////////////////////////////////////////////////////////

        $meet_api = new Meet();
        $course_api = new Course($request);
        $course_info = $course_api->getCourse($courseId);
        $course = json_decode($course_info->body);
        $activities = $events_activities = $meet_api->getUserActivities($request,$courseId,$student_email);

        $data = array(
            'courseName'=>$course->name,
            'email'=>$student_email,
            'activities'=>$activities
        );

        return view('statics.meet')->with($data);
    }
}
