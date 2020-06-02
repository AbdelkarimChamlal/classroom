<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/signin',function(){
    return view('signin');
});

Route::get('/auth',function(){
    return view('auth');
});



Route::post('/validator','auth@validator');
Route::get('/validator',function(){
    return redirect('/');
});

Route::post('/0auth','auth@auth0');
Route::get('/0auth',function(){
    return redirect('/');
});

Route::get('/', 'Pages@main');
Route::get('/signout','Pages@signOut');
Route::get('/create', 'Pages@create');
Route::get('/create/mass','Pages@createMass');
Route::get('/courses','Pages@show');
Route::post('/createCourse','Pages@createCourse');
Route::post('/executeCreationOnMass','Pages@executeCreationOnMass');
Route::get('/courses/{stats}','Pages@show');
Route::post('/coursesIndex','Pages@coursesIndex');
Route::get('/courses/report/{id}','Pages@showReport');


//statics stuff here


//courseV2 stuff here 
Route::get('/statics/export/data',function(){
    return redirect('/statics/export');
});
Route::post('/statics/export/data','Statics@exportCourses');
Route::get('/statics/export','Statics@export');
Route::post('/statics/export','Statics@exportConfirmed');


Route::get('/statics/all','Statics@allGeneralStatics');
Route::post('statics/all','Statics@allGeneralStaticsFilter');

Route::get('/statics/all/{id}','Statics@generalStatics');

Route::get('/statics','Statics@my_courses');
Route::post('/statics','Statics@coursesIndex');

Route::get('/statics/{stats}','Statics@my_courses');

Route::get('/statics/id/{courseId}','Statics@course');
Route::get('/statics/id/{courseId}/drive/{email}','Statics@driveStatics');
Route::get('statics/id/{courseId}/meet/{student_email}','Statics@meetStatics');


//courseV2 stuff here 
Route::get('/coursesV2/export/data',function(){
    return redirect('/coursesV2/export');
});
Route::post('/coursesV2/export/data','Courses@exportCourses');
Route::get('/coursesV2/export','Courses@export');
Route::post('/coursesV2/export','Courses@exportConfirmed');

Route::post('/coursesV2/copy/{courseId}','Courses@copyConfirmed');
Route::get('/coursesV2/copy/{courseId}','Courses@copy');

Route::get('/coursesV2/add/{courseId}','Courses@add');
Route::post('/coursesV2/add/{courseId}','Courses@addPost');

Route::get('/coursesV2/create', 'Courses@create');
Route::post('/coursesV2/createCourse','Courses@createCourse');

Route::get('/coursesV2/createMass','Courses@createMass');
Route::post('/coursesV2/createMass','Courses@createMassExcute');

Route::get('/coursesV2/delete/{courseId}','Courses@deleteCourse');
Route::post('/coursesV2/delete/{courseId}','Courses@deleteCourseConfirmed');

Route::get('/coursesV2/delete/teacher/{teacherId}/course/{courseId}','Courses@deleteTeacher');
Route::post('/coursesV2/delete/teacher/{teacherId}/course/{courseId}','Courses@deleteTeacherConfirmed');
Route::get('/coursesV2/delete/student/{studentId}/course/{courseId}','Courses@deleteStudent');
Route::post('/coursesV2/delete/student/{studentId}/course/{courseId}','Courses@deleteStudentConfirmed');
Route::get('/coursesV2/{id}','Courses@showCourse');




Route::get('/coursesV2','Courses@showAll');
Route::post('/coursesV2','Courses@showFiltered');
Route::get('/coursesV2/report/{id}','Courses@showReport');
Route::get('/coursesV2/edit/{id}','Courses@editCourse');
Route::post('/coursesV2/edit/{id}','Courses@updateCourse');

//testing stuff
Route::get('/get_all_teachers','Test@getTeacherMeetingData');
Route::get('/get_all_v23','Test@getMeetingData');
Route::get('/refresh_access','Test@refresh_access_token');
Route::get('/all_v3','Test@get_all_classroom_v3');
Route::get('/all_v2','Test@get_all_classroom_v2');
Route::get('/teacher_request','Test@get_all_classroom');
Route::get('/all_statics','Test@allStatics');
Route::get('/all_drive_dtatics','Test@allDrive');
Route::get('/db','Pages@db_test');
Route::get('/meet_test','Test@getMeetings');
Route::get('/api_test','Pages@access_token');
Route::get('/drive_test','Pages@get_all_folder_items');
Route::get('/user_activity_test','Pages@get_user_acitivity');
Route::get('/classroom_acitivities','Pages@getUserActivitiesInAClassroom');
Route::get('/date_test',function(){
    $date = "2020-04-16T00:01:19.389Z";
    echo "$date<br>";
    $time = strtotime($date) + 3600;
    echo $time;
    $newDate = date(DATE_RFC3339,$time);
    echo "<br>$newDate";
});

