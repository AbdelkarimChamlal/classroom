@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Raleway&display=swap');


        h1{
            text-align: center;
            /* font-family: 'Indie Flower', cursive; */
            font-family: 'Raleway', sans-serif;

            color:#353555;
            /* font-weight: bold; */
            margin-bottom: 20px;

        }
        h3{
            text-align: center;
            /* font-family: 'Indie Flower', cursive; */
            font-family: 'Raleway', sans-serif;

            color:#aa55aa;
            font-size: 22px;
            margin-bottom: 30px;
        }
        h2{
            margin-top:50px;
            text-align: center;
            /* font-family: 'Indie Flower', cursive; */
            font-family: 'Raleway', sans-serif;
            margin-bottom: 20px;
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
        .contentContainer{
            max-width: 100%;
            height: auto;
            overflow: scroll;
        }
    </style>
@endsection

@section('content')
<h1>Course name : {{$courseName}}</h1>
<h3>Student's Email : {{$email}} </h3>

    @if (count($activities)>0)
        <h2>Events</h2>
        <table class="table">
            <thead>
                <tr>
                  <th scope="col">#Meeting_code</th>
                  <th scope="col">Name</th>
                  <th scope="col">Start</th>
                  <th scope="col">End</th>
                  <th scope="col">Present</th>
                  <th scope="col">Duration</th>
                </tr>
              </thead>
              
                @foreach ($activities as $activity)
                <tbody>
                    <th scope="col">{{$activity->meeting_code}}</th>
                    <td scope="col">{{$activity->title}}</td>
                    <td scope="col">{{date("Y-m-d h:ia",strtotime($activity->start))}}</td>
                    <td scope="col">{{date("Y-m-d h:ia",strtotime($activity->end))}}</td>
                    <td scope="col">{{$activity->present}}</td>
                    <td scope="col">{{$activity->duration}}</td>
                </tbody>
                @endforeach

        </table>
    @else
        <h2>This Classroom has no Events in its Calendar</h2>
    @endif
@endsection