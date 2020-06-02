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
<h4>Information File example : <a href="/resources/example.csv">Example.csv</a></h4>
    {!! Form::open(['url' => '/coursesV2/createMass','files'=>'true']) !!}
        <h4>Upload and execute</h4>
        <div class="row">
            <p class="col-6">Information file (.csv)</p>
            <input type="file" class="col-4 form-control-file" name="file"/>
        </div>
        <div class="row">
            <p class="col-6">Students lists (.txt / .csv)</p>
            <input type="file" class="col-4 form-control-file" name="students[]" multiple />
        </div>
        <div class="row">
            <input type="submit" class=" col-3 btn btn-primary" value="Execute"/>
        </div>
    {!! Form::close() !!}


@endsection