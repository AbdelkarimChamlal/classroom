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
        .contentContainer{
            max-width: 100%;
            height: auto;
            overflow: scroll;
        }
    </style>
@endsection

@section('content')
<h1>Course name : {{$course->name}}</h1>
<h3>Student's Email : {{$email}} </h3>

    @if (count($activities)>0)
    <h3>Students Activities in this classroom Drive</h3>
    <div class="contentContainer">
        <table class="table">
            <thead>
                <tr>
                <th scope="col">File/Folder</th>
                <th scope="col">Type</th>
                <th scope="col">Activity's Type</th>
                <th scope="col">Activity's Date</th>
                <th scope="col">item id</th>
                <th scope="col">item path</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($activities as $activity)
            <tr>
                <div class="row">
                    <td  scope="row">{{$activity->doc_title}}</td>
                    <td  scope="col">{{$activity->doc_type}}</td>
                    <td  scope="col">{{$activity->event_name}}</td>
                    <td  scope="col">{{$activity->event_date}}</td>
                    <td  scope="col-2">{{$activity->doc_id}}</td>
                    <td  scope="col">{{$activity->file_path}}</td>
                </div>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @else
        <h2>This student didn't entracte with this drive</h2>
    @endif
@endsection