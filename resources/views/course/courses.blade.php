@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Dosis:wght@500&display=swap');


        h1{
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#353555;

        }
        h3{
            color:#aa55aa;
            font-size: 22px;
            font-family: 'Dosis', sans-serif;

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
            margin-top:30px;
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
{!! Form::open(['action' => 'Courses@showFiltered', 'method' => 'post']) !!}

        <div class="row" id="filtrage">
            <select id="stats" class="col-2 form-control"  name="establishment">
                <option value="">establishment</option>
                @if(count($establishment)>0)
                    @foreach ($establishment as $f)
                        <option value="{{$f->establishment}}">{{$f->establishment}}</option>
                    @endforeach
                @endif
            </select>
            <select id="stats" class="col-2 form-control" name="diploma">
                <option value="">diploma</option>
                @if(count($diploma)>0)
                    @foreach ($diploma as $f)
                        <option value="{{$f->diploma}}">{{$f->diploma}}</option>
                    @endforeach
                @endif
            </select>
            <select id="stats" class="col-2 form-control" name="filiere">
                <option value="">filiere</option>
                @if(count($filiere)>0)
                    @foreach ($filiere as $f)
                        <option value="{{$f->filiere}}">{{$f->filiere}}</option>
                    @endforeach
                @endif
            </select>
            <select id="stats" class="col-2 form-control" name="semester">
                <option value="">semester</option>
                @if(count($semester)>0)
                    @foreach ($semester as $f)
                        <option value="{{$f->semester}}">{{$f->semester}}</option>
                    @endforeach
                @endif
            </select>
            <select id="stats" class="col-2 form-control" name="stats">
                <option value="">State</option>
                <option value="PROVISIONED">PROVISIONED</option>
                <option value="ACTIVE">ACTIVE</option>
                <option value="ARCHIVED">ARCHIVED</option>
            </select>
            <div class="col-1"></div>
            <input type="submit" id="filter" name ="filter" class="col-1 btn btn-primary" value="Filter">
        </div>
        
{!! Form::close() !!}
  
    @if (count($courses)>0)
    <table class="table">
        <thead>
            <tr>
              <th scope="col">#id</th>
              <td scope="col">Name</td>
              <td scope="col">State</td>
              <td scope="col">Created</td>
              <td scope="col">Edit</td>
              <td scope="col">Add</td>
              <td scope="col">Copy</td>
              <td scope="col">Report</td>
              <td scope="col">Delete</td>

            </tr>
          </thead>
          <tbody>
        @foreach ($courses as $course)
        <tr>
            <th scope="row"><a href="/coursesV2/{{$course->id}}" target="_blank">{{$course->id}}</a></th>
            <td>{{$course->name}}</td>
            <td>{{$course->courseState}}</td>
            <td>{{date("Y-m-d",strtotime($course->creationTime))}}</td>
            <td><a href="/coursesV2/edit/{{$course->id}}" target="_blank"><img id="report_icon" src="{{asset('images/edit.png')}}"/></a></td>
            <td><a href="/coursesV2/add/{{$course->id}}" target="_blank"><img id="report_icon" src="{{asset('images/add.png')}}"/></a></td>
            <td><a href="/coursesV2/copy/{{$course->id}}" target="_blank"><img id="report_icon" src="{{asset('images/copy.png')}}"/></a></td>
            <td><a href="/coursesV2/report/{{$course->id}}" target="_blank"><img id="report_icon" src="{{asset('images/info.PNG')}}"/></a></td>
            <td><a href="/coursesV2/delete/{{$course->id}}" target="_blank"><img id="report_icon" src="{{asset('images/delete.PNG')}}"/></a></td>

        </tr>
        @endforeach
        </tbody>
    </table>

    @else
        <h2>No courses found</h2>
    @endif
@endsection