@extends('../app')

@section('style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');

        .content{
            color:red;
        }
        .content h1{
            color:#353535;
            font-family: 'Dosis', sans-serif;

        }
        .content h5{
            margin-top:30px;
            color:#8585ff;
            font-family: 'Dosis', sans-serif;
            font-weight: bold;
        }
        h3{
            text-align: center;
            font-family: 'Dosis', sans-serif;
            font-weight: bold;
            color:#aa8599;
            font-size: 22px;
        }
        #main-item{
            width:100%;
            padding-left:85px;
            padding-right:85px;
            padding-top:20px;
            padding-bottom:20px;
            height:auto;
        }
    </style>
@endsection

@section('content')

    <h1>Welcome to uae's classroom manager</h1>
    <h5>Services : </h5>

    <div class="row">
        <div class="col-4">

            <a href="/coursesV2">
                <img id="main-item" src="{{asset('images/class.png')}}"/>
                <h3>My Courses</h3>
            </a>

        </div>
        <div class="col-4">

            <a href="/coursesV2/create">
                <img id="main-item" src="{{asset('images/writing.png')}}"/>
                <h3>Create Course</h3>
            </a>

        </div>
        <div class="col-4">

            <a href="/coursesV2/createMass">
                <img id="main-item" src="{{asset('images/mass.png')}}"/>
                <h3>Create on Mass</h3>
            </a>

        </div>
    </div>

@endsection