<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CourseV2;
use App\Response;
use App\Auth;
use App\DataObj;
use ZipArchive;



class Courses extends Controller
{
    //FIXME i should make the copy classroom get the courseId from database if exists and copy it too


    public function exportCourses(Request $request){
        $this->validate($request,[
            'exportChoice'=>'required',
            'export'=>'required',
            'selected'=>'required'
        ]);
        $course_api = new CourseV2();
        //include student lists
        if($request->input('exportChoice')=='with'){
            $selected = $request->input('selected');

              //classroom id
                //classroom name
                //classroom state
                //classroom description
                //classroom headDescription
                //classroom section
                //classroom room
                //classroom calendarId
                //classroom Teachers
                //classroom students count
                
            $data_csv = array(['id','name','state','created at','section','Heading Description','Description','room','calendarId','teachers email','students count','students list filename']);
            $listIndex = 0;
            $listsmap =[];
            foreach($selected as $courseId){
                //new line in the csv file
                $data = [];
                //create an empty row
                for($i=0;$i<12;$i++)$data[$i]="";
                $data[0] = $courseId;

                $course_response = $course_api->getCourse($request,$courseId);
                if($course_response->responseCode==200){
                    $course = json_decode($course_response->body);
                    $data[1] = $course->name;
                    $data[2] = $course->courseState;
                    $data[3] = $course->creationTime;

                    if(property_exists($course,'section')){
                        $data[4] = $course->section;
                    }

                    if(property_exists($course,'descriptionHeading')){
                        $data[5] = $course->descriptionHeading;
                    }

                    if(property_exists($course,'description')){
                        $data[6] = $course->description;
                    }

                    if(property_exists($course,'room')){
                        $data[7] = $course->room;
                    }

                    if(property_exists($course,'calendarId')){
                        $data[8] = $course->calendarId;
                    }

                    $teachers_response = $course_api->getTeachers($request,$courseId);

                    if($teachers_response->responseCode==200){
                        $teachers = json_decode($teachers_response->body);

                        if(property_exists($teachers,'teachers')){
                            foreach($teachers->teachers as $teacher){
                                if(property_exists($teacher->profile,'emailAddress')){
                                    $data[9] = $data[9]."".$teacher->profile->emailAddress." ";
                                }
                            }
                        }
                    }

                    $students = $course_api->getAllStudents($request,$courseId);

                    $data[10] = count($students);

                    $studentsList = [];

                    foreach($students as $student){
                    if(property_exists($student,'profile') && property_exists($student->profile,'emailAddress')){
                        $studentsList[] = $student->profile->emailAddress;
                    }
                    }

                    if($data[10]>0){
                    $now_time = microtime();
                    $fff = fopen('resources/studentsList/'.$now_time.".txt",'w');
                    foreach($studentsList as $studentEmail){
                        fwrite($fff,$studentEmail."\n");
                    }
                    fclose($fff);
                    $data[11] = "list".$listIndex.".txt";
                    $listIndex++;

                    $listsmap[$data[11]]= 'resources/studentsList/'.$now_time.".txt";

                    }

                    $data_csv[] = $data;
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

                $zip->addFile('resources/'.$fileName.'.csv', 'classroom.csv');
            
                    for($i=1;$i<count($data_csv);$i++){
                        if($data_csv[$i][10]>0){
                            $zip->addFile($listsmap[$data_csv[$i][11]],'studentList/'.$data_csv[$i][11]);
                        }
                    }
            
                // All files are added, so close the zip file.
                $zip->close();
            }
            $file= public_path().'/'.$zipFile;
            $headers = array(
                'Content-Type: application/force-download',
              );
            return response()->download($file, 'classroom.zip', $headers);


        }
        else 
        //don't include the students list
        if($request->input('exportChoice')=='without'){
                //classroom id
                //classroom name
                //classroom state
                //classroom description
                //classroom headDescription
                //classroom section
                //classroom room
                //classroom calendarId
                //classroom Teachers
                //classroom students count
            $data_csv = array(['id','name','state','created at','section','Heading Description','Description','room','calendarId','teachers email','students count']);

            $selected = $request->input('selected');
            foreach($selected as $courseId){
                //new line in the csv file
                $data = [];
                //create an empty row
                for($i=0;$i<11;$i++)$data[$i]="";
                $data[0] = $courseId;

                $course_response = $course_api->getCourse($request,$courseId);
                if($course_response->responseCode==200){
                    $course = json_decode($course_response->body);
                    $data[1] = $course->name;
                    $data[2] = $course->courseState;
                    $data[3] = $course->creationTime;

                    if(property_exists($course,'section')){
                        $data[4] = $course->section;
                    }

                    if(property_exists($course,'descriptionHeading')){
                        $data[5] = $course->descriptionHeading;
                    }

                    if(property_exists($course,'description')){
                        $data[6] = $course->description;
                    }

                    if(property_exists($course,'room')){
                        $data[7] = $course->room;
                    }

                    if(property_exists($course,'calendarId')){
                        $data[8] = $course->calendarId;
                    }

                    $teachers_response = $course_api->getTeachers($request,$courseId);

                    if($teachers_response->responseCode==200){
                        $teachers = json_decode($teachers_response->body);

                        if(property_exists($teachers,'teachers')){
                            foreach($teachers->teachers as $teacher){
                                if(property_exists($teacher->profile,'emailAddress')){
                                    $data[9] = $data[9]."".$teacher->profile->emailAddress." ";
                                }
                            }
                        }
                    }

                    $students = $course_api->getAllStudents($request,$courseId);

                    $data[10] = count($students);

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
            return response()->download($file, 'classroom.csv', $headers);

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

            return view('course.export')->with($data);
        }
        return redirect('/');
    }

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

        return view('course.export')->with($data);
    }
    

    public function copyConfirmed(Request $request,$courseId){
        $this->validate($request,[
            'copy'=>'required'
        ]);
        $course_api = new CourseV2();
        $course_response = $course_api->getCourse($request,$courseId);
        if($course_response->responseCode==200){

            $course = json_decode($course_response->body);

            $courseName = $course->name;
            $courseSection = null;
            $courseRoom = null;
            $courseDes = null;
            $courseHDes = null;
            $courseState = null;

            if(property_exists($course,'section')){
                $courseSection = $course->section;
            }

            if(property_exists($course,'description')){
                $courseDes = $course->description;
            }
            
            if(property_exists($course,'descriptionHeading')){
                $courseHDes = $course->descriptionHeading;
            }

            if(property_exists($course,'room')){
                $courseRoom = $course->room;
            }

            if(property_exists($course,'courseState')){
                $courseState = $course->courseState;
            }

            $teachers_response = $course_api->getTeachers($request,$courseId);

            $newCourse_response = $course_api->createCourse($request,$courseName,$courseSection,$courseHDes,$courseDes,$courseRoom,'me',$courseState);


            if($teachers_response->responseCode==200 && $newCourse_response->responseCode==200){
                $teachers = json_decode($teachers_response->body);
                $newCourse = json_decode($newCourse_response->body);

                if(property_exists($teachers,'teachers')){
                    foreach($teachers->teachers as $teacher){
                        if(property_exists($teacher,'profile')){
                            if(property_exists($teacher->profile,'emailAddress')){
                                if($request->session()->get('isAdmin') && strpos($teacher->profile->emailAddress, "uae.ac.ma") !== false){
                                    $course_api->addTeacher($request,$newCourse->id,$teacher->profile->emailAddress);
                                }else{
                                    $course_api->invTeacher($request,$newCourse->id,$teacher->profile->emailAddress);
                                }
                            }
                        }
                    }
                }
            }


            $response = $course_api->archiveClassroom($request,$courseId);

            return redirect('/coursesV2/');
        }


    }

    public function copy(Request $request,$courseId){

        $course_api = new CourseV2();

        $course_response = $course_api->getCourse($request,$courseId);

        if($course_response->responseCode==200){

            $course = json_decode($course_response->body);
            $data = array(
                'courseName'=>$course->name,
                'courseId'=>$course->id
            );

            return view('course.copy')->with($data);
        }
    }

    public function addPost(Request $request,$courseId){
        $this->validate($request,[
            'add'=>'required',
            'userId_list'=>'required',
            'list'=>'required'
        ]);

        $course_api = new CourseV2();

       

            

        if($request->input('userId_list')=='student')
        {
            $students = $request->input('list');

            if($students!=null){

                $students = explode("\n",$students);

                if($request->session()->get('isAdmin')){
                    for($i=0;$i<count($students);$i++){
                        $course_api->addStudent($request,$courseId,$students[$i]);
                    }
                }else{
                    for($i=0;$i<count($students);$i++){
                        $course_api->invStudent($request,$courseId,$students[$i]);
                    }
                }
            }
        }
        else if($request->input('userId_list')=='teacher')
        {
            $teachers = $request->input('list');
            echo $teachers;
            if($teachers!=null){

                $teachers = explode("\n",$teachers);

                if($request->session()->get('isAdmin')){
                    for($i=0;$i<count($teachers);$i++){

                        $course_api->addTeacher($request,$courseId,$teachers[$i]);

                    }
                }else{
                    for($i=0;$i<count($teachers);$i++){

                        $course_api->invTeacher($request,$courseId,$teachers[$i]);

                    }
                }
            }
        }

        return redirect('/coursesV2/'.$courseId);
    }

    public function add(Request $request,$courseId){
        $course_api = new CourseV2();

        $course_response = $course_api->getCourse($request,$courseId);

        if($course_response->responseCode==200){

            $course = json_decode($course_response->body);
            $data = array(
                'courseName'=>$course->name,
                'courseId'=>$course->id
            );

            return view('course.add')->with($data);
        }
    }


    public function deleteCourse(Request $request , $courseId){

        $course_api = new CourseV2();

        $courseInfo = $course_api->getCourse($request,$courseId);
        $courseName ="";
        if($courseInfo->responseCode==200){
            $course=json_decode($courseInfo->body);
            $courseName = $course->name;
        }
        $data = array(
            'courseId'=>$courseId,
            'courseName'=>$courseName
        );
        return view('course.deleteCourse')->with($data);
    }

    public function deleteCourseConfirmed(Request $request,$courseId){
        $this->validate($request,[
            'delete'=>'required'
        ]);
        $course_api = new CourseV2();

        $response = $course_api->deleteCourse($request,$courseId);
        CourseV2::where('id',$courseId)->delete();

        return redirect("coursesV2/");
    }

    public function deleteTeacherConfirmed(Request $request,$teacherId,$courseId){
        $this->validate($request,[
            'delete'=>'required'
        ]);
        $course_api = new CourseV2();

        $response = $course_api->deleteTeacher($request,$courseId,$teacherId);


        return redirect("coursesV2/".$courseId);
    }


    public function deleteTeacher(Request $request,$teacherId,$courseId){

        $course_api = new CourseV2();

        $userInfo = $course_api->getUser($request,$teacherId);

        $tecaherName = "unknown user";

        if($userInfo->responseCode==200){
            $user = json_decode($userInfo->body);
            $tecaherName = $user->name->fullName;
        }

        $courseInfo = $course_api->getCourse($request,$courseId);
        $courseName ="";
        if($courseInfo->responseCode==200){
            $course=json_decode($courseInfo->body);
            $courseName = $course->name;
        }
        $data = array(
            'teacherId'=>$teacherId,
            'courseId'=>$courseId,
            'teacherName'=>$tecaherName,
            'courseName'=>$courseName

        );
        return view('course.deleteTeacher')->with($data);
    }

    public function deleteStudentConfirmed(Request $request,$studentId,$courseId){
        $this->validate($request,[
            'delete'=>'required'
        ]);
        $course_api = new CourseV2();

        $response = $course_api->deleteStudent($request,$courseId,$studentId);


        return redirect("coursesV2/".$courseId);
    }
    public function deleteStudent(Request $request,$studentId,$courseId){

        $course_api = new CourseV2();

        $userInfo = $course_api->getUser($request,$studentId);

        $studentName = "unknown user";

        if($userInfo->responseCode==200){
            $user = json_decode($userInfo->body);
            $studentName = $user->name->fullName;
        }

        $courseInfo = $course_api->getCourse($request,$courseId);
        $courseName ="";
        if($courseInfo->responseCode==200){
            $course=json_decode($courseInfo->body);
            $courseName = $course->name;
        }
        $data = array(
            'studentId'=>$studentId,
            'courseId'=>$courseId,
            'studentName'=>$studentName,
            'courseName'=>$courseName

        );
        return view('course.deleteStudent')->with($data);
    }
    public function showCourse(Request $request , $id){

        $course_api = new CourseV2();

        $course_response = $course_api->getCourse($request,$id);
        if($course_response->responseCode==200){

            $teachers_response = $course_api->getTeachers($request,$id);
            $students = $course_api->getAllStudents($request,$id);
            $course= json_decode($course_response->body);
            $teachers =[];
            if($teachers_response->responseCode==200){
                $teachers_info = json_decode($teachers_response->body);
                if(property_exists($teachers_info,'teachers')){
                    foreach($teachers_info->teachers as $teacher){
                        $teachers[] = $teacher;
                    }
                }
            }
            $data = array('course'=>$course,
                          'teachers'=>$teachers,
                          'students'=>$students
            );
            return view('course.course')->with($data);
        }
    }
    public function updateCourse(Request $request,$id){
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
            'course_name'=>'required',
            'submit'=>'required'
        ]);
        $course_api = new CourseV2();
        $course_response = $course_api->getCourse($request,$id);
        if($course_response->responseCode==200){
            $data = new DataObj();
            $course = json_decode($course_response->body);


            $data->name = $request->input('course_name');

            
            $courseDB = CourseV2::find($id);
            $course_id = $request->input('course_id');
            if($courseDB){
                if($course_id=="" || $course_id==null){
                    $courseDB = CourseV2::find($id)->delete();
                }else{
                    $course_id = explode(":",$course_id);
                    if(isset($course_id[0])){
                        $courseDB->establishment = $course_id[0];
                    }
                    if(isset($course_id[1])){
                        $courseDB->diploma = $course_id[1];
                    }
                    if(isset($course_id[2])){
                        $courseDB->filiere = $course_id[2];
                    }
                    if(isset($course_id[3])){
                        $courseDB->semester = $course_id[3];
                    }
                    $courseDB->save();
                }
            }else{
                if($course_id=="" || $course_id==null){

                }else{
                    $courseID = new CourseV2();
                    $course_id = explode(":",$course_id);
                    $courseID->id = $id;
                    if(isset($course_id[0])){
                        $courseID->establishment = $course_id[0];
                    }
                    if(isset($course_id[1])){
                        $courseID->diploma = $course_id[1];
                    }
                    if(isset($course_id[2])){
                        $courseID->filiere = $course_id[2];
                    }
                    if(isset($course_id[3])){
                        $courseID->semester = $course_id[3];
                    }
                    $courseID->save();
                }
            }

            $descriptionHeading = $request->input('course_descriptionHeading');
            $data->descriptionHeading = $descriptionHeading;
    


            $section = $request->input('course_section');
            $data->section = $section; 
 

            $description = $request->input('course_description');
            $data->description = $description; 

            $room = $request->input('course_room');
            $data->room = $room;
            
            $calendarId = $request->input('course_calendarId');
            $data->calendarId = $calendarId;
            
            $courseState = $request->input('courseState');
            $data->courseState = $courseState; 
  


            

            $update_response = $course_api->updateCourse($request,$id,$data);
            return redirect('/coursesV2');
        }

    }

    public function editCourse(Request $request,$id){


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


        $courseDB = CourseV2::find($id);
        $course_id="";
        if($courseDB){
            $course_id = $courseDB->establishment.":".$courseDB->diploma.":".$courseDB->filiere.":".$courseDB->semester;
        }

        $course_api = new CourseV2();
        $course_response = $course_api->getCourse($request,$id);
        if($course_response->responseCode==200){
            $course = json_decode($course_response->body);
            $calendarId = null;
            $section =null;
            $descriptionHeading=null;
            $description=null;
            $room=null;

            if(property_exists($course,'calendarId')){
                $calendarId = $course->calendarId;
            }
            if(property_exists($course,'section')){
                $section = $course->section;
            }
            if(property_exists($course,'descriptionHeading')){
                $descriptionHeading = $course->descriptionHeading;
            }
            if(property_exists($course,'description')){
                $description = $course->description;
            }
            if(property_exists($course,'room')){
                $room = $course->room;
            }
            $data = array(
                'course_id'=>$course_id,
                'course'=>$course,
                'section'=>$section,
                'descriptionHeading'=>$descriptionHeading,
                'description'=>$description,
                'room'=>$room,
                'calendarId'=>$calendarId,
            );
            return view('course.edit')->with($data);
        }
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

        $course_api = new CourseV2();
        $course = $course_api->getCourse($request,$id);
        $teachers = $course_api->getTeachers($request,$id);
        $students = $course_api->getStudents($request,$id);
        echo "<pre>";
        echo "Course information : ";
        echo var_dump($course->body);
        echo "Teachers :";
        echo var_dump($teachers->body);
        echo "Students : ";
        echo var_dump($students->body);
        echo"</pre>";
    }

    public function createMassExcute(Request $request){
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
            'file'=>'required',
            'students'=>'required'
        ]);


        $file_start = time();
        $file = $request->file('file');
        $students = $request->file('students');
        $fileName = $file_start."".$file->getClientOriginalName();
        $file->move('resources', $fileName);
        $fp = fopen('resources/'.$fileName, "r");
        $courses = [];
        while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) 
        {
          // Each individual array is being pushed into the nested array
          $courses[] = $data;		
        }

        fclose($fp);

        $students_list = [];
        foreach($students as $student){
            $fileName = $file_start."".$student->getClientOriginalName();
            $student->move('resources', $fileName);
            $item = array(
                'filename'=>$student->getClientOriginalName(),
                'fileurl'=>$fileName
            );
            $students_list[] = $item;
        }
        $course_api = new CourseV2();
        for($i=1 ; $i<count($courses) ; $i++){


            $course = $courses[$i];
            $course_id = $course[0];
            $course_name = $course[1];
            $select = $course[2];
            $descriptionHeading=$course[3];
            $description = $course[4];
            $room=$course[5];
            $calendarId=$course[7];
            $courseState = $course[6];
            
            $response = $course_api->createCourse($request,$course_name,$select,$descriptionHeading,$description,$room,'me',$courseState,$calendarId);
            if($response->responseCode==200){
                $course_info = json_decode($response->body);
                $courseId = $course_info->id;
                if($course_id!=null){
                    $course_db = new CourseV2();
                    $course_id = explode(':',$course_id);
                    if(isset($course_id[0])){
                        $course_db->establishment = $course_id[0];
                    }
                    if(isset($course_id[1])){
                        $course_db->diploma = $course_id[1];
                    }
                    if(isset($course_id[2])){
                        $course_db->filiere = $course_id[2];
                    }
                    if(isset($course_id[3])){
                        $course_db->semester = $course_id[3];
                    }
                    $course_db->id = $courseId;
                    $course_db->save();
                }
    
                $teachers = $course[8];
    
                if($teachers!=null){
    
                    $teachers = explode(':',$teachers);
    
                    if($request->session()->get('isAdmin')){
                        for($j=0;$j<count($teachers);$j++){
    
                            $course_api->addTeacher($request,$courseId,$teachers[$j]);
    
                        }
                    }else{
                        for($j=0;$j<count($teachers);$j++){
    
                            $course_api->invTeacher($request,$courseId,$teachers[$j]);
    
                        }
                    }
                }
    
                $student_file = $course[9];
                $item_name = null;

                for($h = 0; $h<count($students_list);$h++){
                    if($student_file == $students_list[$h]['filename']){
                        $item_name = $students_list[$h]['fileurl'];
                    break;
                    }
                }

                
                if($item_name!=null){
                    $student_list = fopen('resources/'.$item_name,'r');

                    $list = [];
                    while(!feof($student_list)){
                        //store the students in an array
                        $list[] = fgets($student_list);
                    }
                    fclose($student_list);
        
                    if($list!=null){
        
                        if($request->session()->get('isAdmin')){
                            for($j=0;$j<count($list);$j++){
                                $course_api->addStudent($request,$courseId,$list[$j]);
                            }
                        }else{
                            for($j=0;$j<count($list);$j++){
                                $course_api->invStudent($request,$courseId,$list[$j]);
                            }
                        }
                    }
                }
            }
        }
        return redirect('/coursesV2');
    }

    public function createMass(Request $request){
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
      return view('course.createMass');
    }

    public function create(Request $request){
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
      ////////////////////////////////////////auth part end/////////////////////////////////////////////

      return view('course.create');
    }


    public function createCourse(Request $request){
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
            'course_name'=>'required'
        ]);
        $course_id = $request->input('course_id');
        $course_name = $request->input('course_name');
        $select = $request->input('course_select');
        $descriptionHeading=$request->input('course_descriptionHeading');
        $description = $request->input('course_description');
        $room=$request->input('course_room');
        $calendarId=$request->input('course_calendarId');
        $courseState = $request->input('courseState');

        $course_api = new CourseV2();
        $response = $course_api->createCourse($request,$course_name,$select,$descriptionHeading,$description,$room,'me',$courseState,$calendarId);
        if($response->responseCode==200){
            $course_info = json_decode($response->body);
            $courseId = $course_info->id;

            if($course_id!=null){
                $course_id = explode(':',$course_id);
                if(isset($course_id[0])){
                    $course_api->establishment = $course_id[0];
                }
                if(isset($course_id[1])){
                    $course_api->diploma = $course_id[1];
                }
                if(isset($course_id[2])){
                    $course_api->filiere = $course_id[2];
                }
                if(isset($course_id[3])){
                    $course_api->semester = $course_id[3];
                }
                $course_api->id = $courseId;
                $course_api->save();
            }

            $teachers = $request->input('teacher_email');

            if($teachers!=null){

                $teachers = explode(',',$teachers);

                if($request->session()->get('isAdmin')){
                    for($i=0;$i<count($teachers);$i++){

                        $course_api->addTeacher($request,$courseId,$teachers[$i]);

                    }
                }else{
                    for($i=0;$i<count($teachers);$i++){

                        $course_api->invTeacher($request,$courseId,$teachers[$i]);

                    }
                }
            }

            $students = $request->input('list');

            if($students!=null){

                $students = explode("\n",$students);

                if($request->session()->get('isAdmin')){
                    for($i=0;$i<count($students);$i++){
                        $course_api->addStudent($request,$courseId,$students[$i]);
                    }
                }else{
                    for($i=0;$i<count($students);$i++){
                        $course_api->invStudent($request,$courseId,$students[$i]);
                    }
                }
            }
        }
        return redirect('/coursesV2');
    }

    public function showAll(Request $request){
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
    
            return view('course.courses')->with($data);
        }
        return redirect('/');
    }




    public function showFiltered(Request $request){

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

        return view('course.courses')->with($data);
    }
}
