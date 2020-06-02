@extends('../app')

@section('style')
<style>
@import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Ubuntu&display=swap');


    h1{
        text-align: center;
        font-family: 'Indie Flower', cursive;
        color:#353555;
        margin-bottom: 50px;
    }
    p{
        font-family: 'Indie Flower', cursive;
        color:#353555;
        font-size: 16px;
        font-weight: 600;
    }
    h4{
        margin-top: 20px;
        margin-bottom: 20px;
        font-family: 'Indie Flower', cursive;
        color:#353555;

    }
</style>
@endsection

@section('content')
    <h1>Create multiple courses at once</h1>
    <p>to create multiple courses at please prepare a .CSV file with the following attributes</p>
    <div class="row">
        <p class="col-6">1 - COURSE NAME ( REQUIRED )</p>
        <p class="col-6">2 - COURSE SECTION </p>
        <p class="col-6">3 - COURSE HEAD DISCRIPTION </p>
        <p class="col-6">4 - COURSE DISCRIPTION </p>
        <p class="col-6">5 - COURSE STATE <small><b>( ACTIVE ARCHIVED PROVISIONED )</b></small> </p>
        <p class="col-6">6 - COURSE ROOM</p>
        <p class="col-6">7 - CALENDER ID </p>
        <p class="col-6">8 - TEACHERS EMAILS <small>use ":" to split between more than one teacher email</small></p>
        <p class="col-6">9 - STUDENTS</p>
        <h4 class="col-12">CSV FILE EXAMPLE : </h4>
        <img class="col-12" src="{{asset('images/csv.PNG')}}"/>
    </div>
    {!! Form::open(['url' => '/executeCreationOnMass','files'=>'true']) !!}
        <h4>Upload and execute</h4>
        <div class="row">
            <p class="col-2">.CSV file</p>
            <input type="file" class="col-4 form-control-file" name="file"/>
            <input type="submit" class=" col-3 btn btn-primary" value="Execute"/>
        </div>
    {!! Form::close() !!}


@endsection