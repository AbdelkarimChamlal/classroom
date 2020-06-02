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
    <h1>Edit Course</h1>
    <div class="row">
        <h3 class="col-12">Course Information </h3>
        <div class="col-10">
            <form action="/coursesV2/edit/{{$course->id}}" method="POST">
            {{-- {!! Form::open(['url' => "/coursesV2/edit/{{$course->id}}", 'method' => 'post']) !!} --}}
                <div class="row">
                    <label class="col-4">Course Id (  optional  )  : </label>
                <input type ="text" name="course_id" class="form-control col-8" value="{{$course_id}}" />
                </div>
                <div class="row">
                    <label class="col-4">Course name (  required  )  : </label>
                    <input type ="text" name="course_name" class="form-control col-8" value="{{$course->name}}" />
                </div>
                <div class="row">
                    <label class="col-4">section (  optinal  )  : </label>
                    <input type ="text" name="course_section" class="form-control col-8" value="{{$section}}" />
                </div>
                <div class="row">
                    <label class="col-4">description Heading (  optional  )  : </label>
                    <input type ="text" name="course_descriptionHeading" class="form-control col-8" value="{{$descriptionHeading}}" />
                </div>
                <div class="row">
                    <label class="col-4">description (  optional  )  : </label>
                    <input type ="text" name="course_description" class="form-control col-8" value="{{$description}}" />
                </div>
                <div class="row">
                    <label class="col-4">room (  optional  )  : </label>
                    <input type ="text" name="course_room" class="form-control col-8" value="{{$room}}" />
                </div>
                <div class="row">
                    <label class="col-4">Calendar Id (  optional  )  : </label>
                    <input type ="text" name="course_calendarId" class="form-control col-8" value="{{$calendarId}}" />
                </div>
                <div class="row">
                    <label class="col-4">Course State (  optional  )  : </label>
                    <select id="stats" class="col-8 form-control" name="courseState">
                        <option value="{{$course->courseState}}">{{$course->courseState}}</option>
                        <option value="">------------</option>
                        <option value="PROVISIONED">PROVISIONED</option>
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="ARCHIVED">ARCHIVED</option>
                    </select>
                </div>
            

            <div class="row">
                <div class="col-4"></div>
                <input type="submit" value="Save" name="submit" id="submit_btn" class="col-4 btn btn-primary"/>
            </div>
            {{-- {!! Form::close() !!} --}}
            </form>
        </div>
    </div>

@endsection