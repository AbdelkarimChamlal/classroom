@extends('app')

@section('style')
<style>
        #username{
            margin-bottom:20px;
        }

    </style>
@endsection
<?php 
$scope = "https://www.googleapis.com/auth/classroom.courses https://www.googleapis.com/auth/classroom.rosters https://www.googleapis.com/auth/classroom.coursework.students https://www.googleapis.com/auth/admin.directory.user.readonly https://www.googleapis.com/auth/classroom.profile.emails https://www.googleapis.com/auth/admin.reports.audit.readonly https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive.metadata https://www.googleapis.com/auth/drive.metadata.readonly https://www.googleapis.com/auth/drive.readonly https://www.googleapis.com/auth/drive.appdata https://www.googleapis.com/auth/drive.metadata.readonly https://www.googleapis.com/auth/drive.photos.readonly https://www.googleapis.com/auth/drive.appdata https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/classroom.announcements https://www.googleapis.com/auth/classroom.coursework.me";
$url = "https://accounts.google.com/o/oauth2/v2/auth?scope=email%20profile ".$scope."&response_type=code&redirect_uri=http://localhost/classroom/public/0auth.php&client_id=94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com&access_type=offline
";
?>
@section('content')
    <h1 id="username">Hello {{$username}},</h1>

    <p>it looks like you didn't give us access to your classroom or the session ended between us and google</p>
    <small> please note that to use our service you have to give us access to manage your classroom. </small><br>
<a href="{{$url}}" class="btn btn-primary">Give us access</a>
@endsection

