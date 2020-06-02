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
            color:red;
        }
    </style>
@endsection

@section('content')
<div class="alert alert-warning" role="alert">
    <h3>
    Are you sure you want to delete the classroom <b>{{$courseName}}</b><br></h3>
    <h5>please take a moment before you make this action ,because you can not undo this action and you will lose all the data that are relevant to this classroom</h5>

    {!! Form::open(['url' => "/coursesV2/delete/{$courseId}", 'method' => 'post']) !!}
    <div class="row">
        <input type="submit" value="Delete" name="delete" id="submit_btn" class="col-3 btn btn-primary"/>
    </div>
    {!! Form::close() !!}
  </div>
@endsection