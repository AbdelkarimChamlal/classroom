@extends('../app')

@section('style')
    <style>

    </style>
@endsection

@section('content')
    <h1>{{$courseName}}</h1>

    {!! Form::open(['url' => "/coursesV2/add/{$courseId}", 'method' => 'post']) !!}
    <h4>add students or teachers to this classroom</h4>
    <p>
        Emails or Massar Codes
        <br>
        <small>
            please put each email/massarCode in new line
        </small>
    </p>
    

    <textarea class="form-control col-8" name="list" placeholder="student_one@etu.uae.ac.ma
H123456789
teacher@uae.ac.ma
student_four@etu.uae.ac.ma
..."rows="6"></textarea>
    <div class="row">
        <div class="col-4 row">
            <h5 class="col-7">Add Teachers</h5><input type ="radio" checked="checked" name="userId_list" value="teacher"  class="form-control col-1"/>
        </div>
        <div class="col-4 row">
            <h5 class="col-7">Add Students</h5><input type ="radio" name="userId_list" value="student"  class="form-control col-1"/>
        </div>
    </div>
    <div class="row">
        <input type="submit" value="Add" name="add" id="submit_btn" class="col-2 btn btn-primary"/>
    </div>

    {!! Form::close() !!}


@endsection