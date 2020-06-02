@extends('../app')

@section('style')

    <script src="https://www.chartjs.org/samples/latest/utils.js"></script>
    <script src="https://www.chartjs.org/dist/2.9.3/Chart.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap');
        h2{
            font-family: 'Patrick Hand', cursive;
            color:#353535;
            margin:20px;
        }
    </style>
@endsection

@section('content')

    <h1>{{$classroom->name}}</h1>
    <h2>Classroom Statics</h2>
    <div class="row">
        <table class="col-12 table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Statics</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">Students count</th>
                <td>{{$classroom->studentCount}}</td>
            </tr>
            <tr>
                <th scope="row">Announcement count</th>
                <td>{{$classroom->announcementCount}}</td>
            </tr>
            <tr>
                <th scope="row">CourseWork count</th>
                <td>{{$classroom->courseWorkCount}}</td>
            </tr>
            <tr>
                <th scope="row">Student Submiting CourseWork Ratio</th>
                <td>{{number_format((float)$classroom->submitRatio,2,'.','')}} %</td>
            </tr>
            </tbody>
        </table>
        <canvas class="col-12" id="classroom">

        </canvas>
        <script>
            new Chart(document.getElementById("classroom"), {
                type: 'pie',
                data: {
                labels: ["Submited", "Not Submited"],
                datasets: [{
                    label: "Averge courseWork submition percent (%)",
                    backgroundColor: ["#5555ff", "#ff5555"],
                    data: [{{number_format((float)$classroom->submitRatio,2,'.','')}},{{(100 - number_format((float)$classroom->submitRatio,2,'.',''))}}]
                }]
                },
                options: {
                title: {
                    display: true,
                    text: 'Averge courseWork submition percent (%)'
                }
                }
            });
        </script>
    </div>
    <h2>Drive Statics</h2>
    <div class="row">
        <table class="col-12 table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Statics</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">Drive file count</th>
                <td>{{$drive->total}}</td>
            </tr>
            <tr>
                <th scope="row">images</th>
                <td>{{$drive->images}}</td>
            </tr>
            <tr>
                <th scope="row">videos</th>
                <td>{{$drive->videos}}</td>
            </tr>
            <tr>
                <th scope="row">audios</th>
                <td>{{$drive->audios}}</td>
            </tr>
            <tr>
                <th scope="row">documents</th>
                <td>{{$drive->documents}}</td>
            </tr>
            <tr>
                <th scope="row">others</th>
                <td>{{$drive->others}}</td>
            </tr>
            </tbody>
        </table>
        <canvas id="drive" class="col-12"></canvas>
        <script>
            new Chart(document.getElementById("drive"), {
                type: 'doughnut',
                data: {
                labels: ["videos", "audios", "documents", "images", "others"],
                datasets: [
                    {
                    label: "types",
                    backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
                    data: [{{$drive->videos}},{{$drive->audios}},{{$drive->documents}},{{$drive->images}},{{$drive->others}}]
                    }
                ]
                },
                options: {
                title: {
                    display: true,
                    text: 'Files type in the classroom\'s drive folder'
                }
                }
            });

        </script>
    </div>
    <h2>Calendar Statics</h2>
    <div class="row">
        <table class="col-12 table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Statics</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">Events Count</th>
                <td>{{$calendar->eventsCount}}</td>
            </tr>
            <tr>
                <th scope="row">Meetings on Meet</th>
                <td>{{$calendar->meetingCount}}</td>
            </tr>
            <tr>
                <th scope="row">The Averge number of people that Attende the meetings </th>
                <td>{{$calendar->meetingPresentRatio}}</td>
            </tr>
            </tbody>
        </table>
        <canvas class="col-12" id="calendar">
        <?php
            $calendarA = 0;
            if($classroom->studentCount>0){
                $calendarA = ($calendar->meetingPresentRatio/$classroom->studentCount)*100;
            }
            
            $calendarB=0;
            if($calendarA<=100){
                $calendarB = 100 - $calendarA;
            }
        ?>
        <script>
        new Chart(document.getElementById("calendar"), {
            type: 'pie',
            data: {
            labels: ["Attende", "Didn't Attende"],
            datasets: [{
                label: "Averge meeting Attendees percent (%)",
                backgroundColor: ["#5555ff", "#ffffff"],
                data: [{{$calendarA}},{{$calendarB}}]
            }]
            },
            options: {
            title: {
                display: true,
                text: 'Averge People Attendees / Student Count (%)'
            }
            }
        });
        </script>
    </div>


@endsection