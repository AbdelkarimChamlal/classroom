@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');

        h1{
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#353555;

        }
        h3{
            text-align: center;
            font-family: 'Dosis', sans-serif;
            color:#aa55aa;
            font-size: 22px;
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
    <h1>All Statics</h1>

    {!! Form::open(['action' => 'Statics@allGeneralStaticsFilter', 'method' => 'post']) !!}

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
              <th scope="col">Name</th>
              <th scope="col">State</th>
              <th scope="col">Creation Time</th>


            </tr>
          </thead>
          <tbody>
        @foreach ($courses as $course)
        <tr>
        <th scope="row"><a href="/statics/all/{{$course->id}}">{{$course->id}}</a></th>
            <td>{{$course->name}}</td>
            <td>{{$course->courseState}}</td>
            <td>{{date("Y-m-d",strtotime($course->creationTime))}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @else
        <h2>No courses found</h2>
    @endif
@endsection