@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');

        h1{
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#353555;
            font-weight: bold;
            margin-bottom: 20px;

        }
        h3{
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#aa55aa;
            font-size: 22px;
            margin-bottom: 30px;
        }
        h2{
            margin-top:50px;
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#6666ff;
            font-weight: bold;
        }
        #filter{
            padding:3px;
            height:40px;
        }
        #filtrage{
            margin-top:50px;
            margin-bottom: 30px;
        }
        #report_icon{

            height:25px;
            width: 25px;
        }
    </style>
@endsection

@section('content')
<h1>Course name : {{$course->name}}</h1>

    @if (count($emails)>0)
    <h3>Students list</h3>
    <table class="table">
        <thead>
            <tr>
              <th scope="col">#Email</th>
              <th scope="col">Drive Statics</th>
              <th scope="col">Meet  Statics</th>
            </tr>
          </thead>
          <tbody>
        @foreach ($emails as $email)
        <tr>
        <th scope="row">{{$email}}</th>
        <td><a target="_blanc"href="/statics/id/{{$course->id}}/drive/{{$email}}"><img id="report_icon" src="{{asset('images/drive.png')}}"/></a></td>
        <td><a target="_blanc"href="/statics/id/{{$course->id}}/meet/{{$email}}"><img id="report_icon" src="{{asset('images/meet.png')}}"/></a></td>
           
        </tr>
        @endforeach
        </tbody>
    </table>

    @else
        <h2>There is no student's in this course</h2>
    @endif
@endsection