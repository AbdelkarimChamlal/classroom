@extends('../app')

@section('style')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Balsamiq+Sans&family=Source+Code+Pro:wght@300&display=swap');

    input{
        margin:20px;
    }
    h3{
        color:#666666;
        padding:20px;
        padding-bottom: 0px;
        font-family: 'Balsamiq Sans', cursive;
    }
    h5{
        padding:20px;
        padding-top: 0px;

        font-family: 'Balsamiq Sans', cursive;
        color:#353588;
    }
</style>
@endsection

@section('content')

<div class="alert alert-info" role="alert">
    <h3>
        MAKE A NEW COPY OF <b>{{$courseName}}</b>
    </h3>
    <br>
    <h5>please note that this action will create a new classroom with the same details as <b>{{$courseName}}</b> and the current classroom will be <b>ARCHIVED</b></h5>
    
    {!! Form::open(['url' => "/coursesV2/copy/{$courseId}", 'method' => 'post']) !!}
    <div class="row">
        <input type="submit" value="Copy" name="copy" id="submit_btn" class="col-2 btn btn-primary"/>
    </div>
    {!! Form::close() !!}
  </div>


@endsection