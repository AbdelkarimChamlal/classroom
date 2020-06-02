@extends('../app')

@section('style')
    <style>
        input{
            margin:10px;
        }
    </style>
@endsection

@section('content')
<div class="alert alert-primary" role="alert">
    Are you sure you want to delete <b>{{$studentName}}</b> from the classroom  <b>{{$courseName}}</b>

    {!! Form::open(['url' => "/coursesV2/delete/student/{$studentId}/course/{$courseId}", 'method' => 'post']) !!}
    <div class="row">
        <input type="submit" value="Delete" name="delete" id="submit_btn" class="col-3 btn btn-primary"/>
    </div>
    {!! Form::close() !!}
  </div>
@endsection