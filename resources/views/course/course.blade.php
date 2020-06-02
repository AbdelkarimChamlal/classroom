@extends('../app')

@section('style')
    <style>
        h3{
            margin:20px;
        }
        small{
            margin:10px;
        }
    </style>
  
@endsection

@section('content')
<h1>{{$course->name}}</h1>

<h3>Teachers : </h3>



@if (count($teachers)>0)
<table class="table">
    <thead>
        <tr>
          <th scope="col">Name</th>
          <td scope="col">Email</td>
          <td scope="col">Delete</td>
        </tr>
    </thead>
    <tbody>
    @foreach ($teachers as $teacher)
    <tr>
        <td>{{$teacher->profile->name->fullName}}</td>
        <td>{{$teacher->profile->emailAddress}}</td>
        <th scope="row"><a href="/coursesV2/delete/teacher/{{$teacher->userId}}/course/{{$course->id}}" target="_blank">delete</a></th>
    </tr>
    @endforeach
    </tbody>
</table>
@else
    <small>this classroom have no teachers in it</small>
@endif
    
<h3>Students : </h3>

@if (count($students)>0)
<small>Students count {{count($students)}}</small>
<table class="table">
    <thead>
        <tr>
          <th scope="col">Name</th>
          <td scope="col">Email</td>
          <td scope="col">Delete</td>
        </tr>
    </thead>
    <tbody>
    @foreach ($students as $student)
    <tr>

        <td>{{$student->profile->name->fullName}}</td>

        @if (property_exists($student->profile,'emailAddress'))
            <td>{{$student->profile->emailAddress}}</td>
        @else
            <td>Student Email dosn't belong to your domain</td>
        @endif

        <th scope="row"><a href="/coursesV2/delete/student/{{$student->userId}}/course/{{$course->id}}" target="_blank">delete</a></th>
    </tr>
    @endforeach
    </tbody>
</table>
@else
    <small>this classroom have no students in it</small>
@endif
@endsection