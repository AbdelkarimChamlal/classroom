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
    h5{
        margin-top:5px;
        text-align: center;
        font-family: 'Dosis', sans-serif;
        color:#a0a0a0;
        font-weight: bold;
        font-size: 18px;
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
    .export{
        padding-left: 20px;
    }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    });
</script>
@endsection

@section('content')
<h1>Export Classroom Data</h1>
{!! Form::open(['action' => 'Courses@exportConfirmed', 'method' => 'post']) !!}

        <div class="row" id="filtrage">
            <select  class="col-2 form-control"  name="establishment">
                <option value="">establishment</option>
                @if(count($establishment)>0)
                    @foreach ($establishment as $f)
                        <option value="{{$f->establishment}}">{{$f->establishment}}</option>
                    @endforeach
                @endif
            </select>
            <select class="col-2 form-control" name="diploma">
                <option value="">diploma</option>
                @if(count($diploma)>0)
                    @foreach ($diploma as $f)
                        <option value="{{$f->diploma}}">{{$f->diploma}}</option>
                    @endforeach
                @endif
            </select>
            <select class="col-2 form-control" name="filiere">
                <option value="">filiere</option>
                @if(count($filiere)>0)
                    @foreach ($filiere as $f)
                        <option value="{{$f->filiere}}">{{$f->filiere}}</option>
                    @endforeach
                @endif
            </select>
            <select  class="col-2 form-control" name="semester">
                <option value="">semester</option>
                @if(count($semester)>0)
                    @foreach ($semester as $f)
                        <option value="{{$f->semester}}">{{$f->semester}}</option>
                    @endforeach
                @endif
            </select>
            <select  class="col-2 form-control" name="stats">
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
    {!! Form::open(['action' => 'Courses@exportCourses', 'method' => 'post']) !!}

    <table class="table">
        <thead>
            <tr>
              <td scope="col"><input type="checkbox" id="checkAll"> All</td>
              <td scope="col">Name</td>
              <td scope="col">State</td>
              <td scope="col">Created</td>
            </tr>
          </thead>
          <tbody>
        @foreach ($courses as $course)
        <tr>

            <th scope="row"><input type="checkbox" name='selected[]' value="{{$course->id}}"/></th>
            <td>{{$course->name}}</td>
            <td>{{$course->courseState}}</td>
            <td>{{date("Y-m-d",strtotime($course->creationTime))}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="export row">
        <div class="col-4 row">
            <h5 class="col-7">
                include students
            </h5>
            <input type ="radio" checked="checked" height="10px" name="exportChoice" value="with"  class="form-control col-1"/>
        </div>
        <div class="col-4 row">
            <h5 class="col-7">
                without students
            </h5>
            <input type ="radio" name="exportChoice" height="10px" value="without"  class="form-control col-1"/>
        </div>
        <input type="submit" id="filter" name ="export" class="col-3 btn btn-primary" value="Export">
    </div>


    {!! Form::close() !!}
    @else
        <h2>No courses found</h2>
    @endif
@endsection


