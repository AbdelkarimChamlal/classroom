<html>
    <head>
        <meta name="google-signin-scope" content="profile email">
        <meta name="google-signin-client_id" content="94273957467-cufu3gjjbchnor6ugh14bldf6qlf7h3i.apps.googleusercontent.com">
        <script src="https://apis.google.com/js/platform.js"></script>
        {{-- <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script> --}}
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@500&display=swap');

            .body{
                background-color: #cacaca;
                margin:0;
                padding: 0;
            }
            .main{
                margin-top:40vh;
                width:100%;
                height:auto;
                text-align: center;
                font-family: 'Source Code Pro', monospace;
            }
            .main h1{
                margin-bottom: 30px;
            }
            .main .g-signin2{
                overflow: hidden;
                display: none;
                margin-top: 30px;
                margin-left: auto;
                margin-right: auto;
                width:0px;
                height:0px;
            }


        </style>
    </head>
    <body>
       <div class="container">
           <div class="main">
               <h1>Sign Out ...</h1>
            <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>

           </div>
       </div>
    </body>
</html>

<script>

function onSignIn(googleUser){
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
        window.location.replace("/");
    });

}
setTimeout(function(){ window.location.replace("/"); }, 10000);


</script>