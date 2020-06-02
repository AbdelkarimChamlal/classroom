<html>
<head>
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="something"></div>
<?php 
if(isset($_GET['code'])){
    $code = $_GET['code'];
    echo"
    <script>
        var url = 'http://classroom.prj/0auth';
        var form = $('<form action=\"' + url + '\" method=\"post\">'+
                    '<input type=\"hidden\" name=\"code\" value=\"".$code."\" />' +
                    '</form>');
        $('body').append(form);
        form.submit();
    </script>\n";
}else if(isset($_GET['error'])){

}else{

}
?>
</body>
</html>





<!-- $url = "https://accounts.google.com/signin/oauth/oauthchooseaccount?scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fclassroom.courses%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fclassroom.coursework.students%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fclassroom.profile.emails%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fclassroom.rosters%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fclassroom.student-submissions.students.readonly%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fclassroom.student-submissions.me.readonly%20email%20profile&response_type=code&redirect_uri=http%3A%2F%2Flocalhost%2Fclassroom%2Fpublic%2F0auth.php&client_id=94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com&access_type=offline&o2v=2&as=RJTY84bIvrEvuOPggZ1yuw&flowName=GeneralOAuthFlow"; -->


