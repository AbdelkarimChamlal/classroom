<!doctype html>
<html lang="en">
  <head>


  	<title>UAE Classroom Manager</title>
    <meta charset="utf-8">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Dosis:wght@500&display=swap');
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    @yield('style')
  </head>
  <body>
		
 <div class="wrapper">
        <div class="row">
            <div class="col-3">
                <nav id="sidebar">
                    <div class="p-4 pt-5">
                        <ul class="list-unstyled components mb-5">
                            <li>
                              <a href="/" >Dashboard</a>
                            </li>
                            <li>
                            <a href="#course" data-toggle="collapse"  class="dropdown-toggle">Courses</a>
                            <ul class="collapse list-unstyled" id="course">
                              <li>
                                  <a href="/coursesV2">My Courses</a>
                              </li>
                              <li>
                                  <a href="/coursesV2/create">Create new Course</a>
                              </li>
                              <li>
                                  <a href="/coursesV2/createMass">Create on Mass</a>
                              </li>
                              <li>
                                <a href="/coursesV2/export">Export</a>
                              </li>
                            </ul>
                            </li>
                            <li>
                            <a href="#statics" data-toggle="collapse"  class="dropdown-toggle" >Statistics</a>
                            <ul class="collapse list-unstyled" id="statics">
                              <li>
                                  <a href="/statics">My Courses</a>
                              </li>
                              <li>
                                  <a href="/statics/all">All statics</a>
                              </li>
                              <li>
                                <a href="/statics/export">Export</a>
                              </li>
                            </ul>
                            </li>
                            <li>
                              <a href="http://classroom.prj/signout" >Sign out</a>
                            </li>
                          </ul>
                    </div>
                </nav>
            </div>
            <div class="col-9 content">
                @yield('content')
            </div>
        </div>
      </div>
    </div>

        

    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script src="{{asset('js/popper.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/main.js')}}"></script>
  </body>
</html>