<?php

// echo "hi";



//scoopes https://www.googleapis.com/auth/classroom.courses
//        https://www.googleapis.com/auth/classroom.coursework.me
//        https://www.googleapis.com/auth/classroom.coursework.students
//        https://www.googleapis.com/auth/classroom.guardianlinks.students
//        https://www.googleapis.com/auth/classroom.profile.emails
//        https://www.googleapis.com/auth/classroom.rosters
//        https://www.googleapis.com/auth/classroom.student-submissions.me.readonly
//        https://www.googleapis.com/auth/classroom.student-submissions.students.readonly
//        https://www.googleapis.com/auth/classroom.topics

$scope = "https://www.googleapis.com/auth/classroom.courses https://www.googleapis.com/auth/classroom.rosters";
$url = "https://accounts.google.com/o/oauth2/v2/auth?
 scope=email%20profile&
 response_type=code&
 redirect_uri=http://localhost/classroom/public/0auth.php&
 client_id=94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com&access_type=offline
";

$body ="code=4/zAGu4CT_4wqA58cbTgNPPDOYADxjNJVJDboNsM-YUnb4LqG9swifsJEEqTmzG86Zxb9Xd_bDMti64nQUI-YUpGU&
client_id=94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com&
client_secret=1yEYELh90BQELyTV2WTqOyws&
redirect_uri=http://localhost/classroom/public/0auth&
grant_type=authorization_code";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"redirect_uri=http://localhost/classroom/public/0auth/&code=4/zAGu4CT_4wqA58cbTgNPPDOYADxjNJVJDboNsM-YUnb4LqG9swifsJEEqTmzG86Zxb9Xd_bDMti64nQUI-YUpGU&client_secret=1yEYELh90BQELyTV2WTqOyws&client_id=94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com&grant_type=authorization_code");
// curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$head = curl_exec($ch);
echo $head;
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// code  = 4/zAGu4CT_4wqA58cbTgNPPDOYADxjNJVJDboNsM-YUnb4LqG9swifsJEEqTmzG86Zxb9Xd_bDMti64nQUI-YUpGU

?>