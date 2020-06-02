@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Dosis:wght@500&display=swap');



        h1{
            font-family: 'Dosis', sans-serif;
            color:#353555;
            margin-bottom: 50px;
        }
        label{
            font-family: 'Dosis', sans-serif;
            color:#353555;
            /* text-align: center; */
            height:50px;
            vertical-align: middle;
            font-size: 16px;
            font-weight: bold;
        }
        input[type=text,type=radio], select {
            height:50px;
            padding: 12px 20px;
            margin: 8px 0;
            font-family: 'Ubuntu', sans-serif;
            font-size: 16px;
            display: inline-block;
            border: 1px solid #ccc;


            border-radius: 4px;
            box-sizing: border-box;
        }
        small{
            margin-left:15px;
            font-family: 'Dosis', sans-serif;
            font-weight: bold;
            font-size: 14px;
        }
        h3{
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#aa55aa;
            font-size: 28px;
            margin-bottom: 20px;
        }
        #radio{
            padding-top: :15px;
            height:30px;
        }
        #submit_btn{
            margin-top: 30px;
            margin-bottom: :30px;
        }
    </style>
@endsection

@section('content')
    <h1>Create new Course</h1>
    <div class="row">
        <h3 class="col-12">Course Information </h3>
        <div class="col-10">
            {!! Form::open(['action' => 'Courses@createCourse', 'method' => 'post']) !!}
                <div class="row">
                    <label class="col-4">Course Id (  optional  )  : </label>
                    <input type ="text" name="course_id" class="form-control col-8" placeholder=" FST:FM:SMI:S4" />
                </div>
                <div class="row">
                    <label class="col-4">Course name (  required  )  : </label>
                    <input type ="text" name="course_name" class="form-control col-8" placeholder="Biology" />
                </div>
                <div class="row">
                    <label class="col-4">section (  optinal  )  : </label>
                    <input type ="text" name="course_section" class="form-control col-8" placeholder="Period 2" />
                </div>
                <div class="row">
                    <label class="col-4">description Heading (  optional  )  : </label>
                    <input type ="text" name="course_descriptionHeading" class="form-control col-8" placeholder="Welcome to Biology" />
                </div>
                <div class="row">
                    <label class="col-4">description (  optional  )  : </label>
                    <input type ="text" name="course_description" class="form-control col-8" placeholder=" We'll be learning about  the structure of living..." />
                </div>
                <div class="row">
                    <label class="col-4">room (  optional  )  : </label>
                    <input type ="text" name="course_room" class="form-control col-8" placeholder=" 301" />
                </div>
                <div class="row">
                    <label class="col-4">Calendar Id (  optional  )  : </label>
                    <input type ="text" name="course_calendarId" class="form-control col-8" placeholder="group@uae.ac.ma" />
                </div>
                <div class="row">
                    <label class="col-4">Course State (  optional  )  : </label>
                    <select id="stats" class="col-8 form-control" name="courseState">
                        <option value="PROVISIONED">PROVISIONED</option>
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="ARCHIVED">ARCHIVED</option>
                    </select>
                </div>
            <h3 class="col-12">Course's Teacher </h3>
            <div class="row">
                <label class="col-4">Teacher's email (  optional  )  : </label>
                <input type ="text" name="teacher_email" class="form-control col-8" placeholder="teacher@uae.ac.ma" />
                <small>for more than one teacher use ' , ' to separate between their emails</small>
            </div>
            <h3 class="col-12">Course's Students </h3>
            <div class="row">
                <div class="col-4">
                    <div class="row">
                        <input type ="radio" checked="checked" name="students_list" value="email" id="radio" class="form-control col-3"/>
                        <label class="form-check-label col-9">
                            add by email
                        </label>
                        <input type ="radio" name="students_list" value="massar" id="radio" class="form-control col-3"/>
                        <label class="form-check-label col-9">
                            add by Massar code
                        </label>
                    </div>
                </div>

                <textarea class="form-control col-8" name="list" 
placeholder=
"student_one@etu.uae.ac.ma
student_two@etu.uae.ac.ma
student_three@etu.uae.ac.ma
student_four@etu.uae.ac.ma
..."
                  rows="6"></textarea>
            </div>

            <div class="row">
                <div class="col-4"></div>
                <input type="submit" value="create" name="submit" id="submit_btn" class="col-4 btn btn-primary"/>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection