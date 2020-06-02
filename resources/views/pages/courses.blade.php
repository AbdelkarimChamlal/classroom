@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');

        h1{
            text-align: center;
            font-family: 'Indie Flower', cursive;
            color:#353555;

        }
        h3{
            text-align: center;
            font-family: 'Indie Flower', cursive;
            color:#aa55aa;
            font-size: 22px;
        }
        h2{
            margin-top:50px;
            text-align: center;
            font-family: 'Indie Flower', cursive;
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
    <h1>My Courses</h1>
{!! Form::open(['action' => 'Pages@coursesIndex', 'method' => 'post']) !!}
        <div class="row" id="filtrage">
            <label for="stats" class="col-4"><h3>Filter :</h3></label>
            <select id="stats" class="col-3 form-control" name="stats">
                <option value="all">all</option>
                <option value="PROVISIONED">PROVISIONED</option>
                <option value="ACTIVE">ACTIVE</option>
                <option value="ARCHIVED">ARCHIVED</option>
            </select>
            <div class="col-1"></div>
            <input type="submit" id="filter" class="col-2 btn btn-primary" value="Filter">
        </div>
        
{!! Form::close() !!}
  
    @if (count($courses)>0)
    <table class="table">
        <thead>
            <tr>
              <th scope="col">#id</th>
              <th scope="col">Name</th>
              <th scope="col">State</th>
              <th scope="col">Creation Time</th>
              <th scope="col">Report</th>

            </tr>
          </thead>
          <tbody>
        @foreach ($courses as $course)
        <tr>
            <th scope="row">{{$course->id}}</th>
            <td>{{$course->name}}</td>
            <td>{{$course->courseState}}</td>
            <td>{{date("Y-m-d",strtotime($course->creationTime))}}</td>
        <td><a href="/courses/report/{{$course->id}}" target="_blank"><img id="report_icon" src="{{asset('images/info.PNG')}}"/></a></td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @else
        <h2>No courses found</h2>
    @endif
@endsection