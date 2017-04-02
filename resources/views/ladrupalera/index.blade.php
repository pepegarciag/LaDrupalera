<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>La Drupalera - Random</title>
    <link rel="stylesheet" href="/main.css">
</head>
<body>
    <div class="bg"></div>
    <div class="container">
        <figure class="card">
            <img src="/dev.png" alt="profile-sample" class="background"/>
            <img src="/dev.png" alt="profile-sample" class="profile"/>
            <figcaption>
                <a href="" id="title" target="_blank">......</a>
                <div class="icons">
                    <a id="twitter" href="#" target="_blank">
                        <i class="ion-social-twitter-outline"></i>
                    </a>
                    <a href="#" id="refresh">
                        <i class="ion-refresh"></i>
                    </a>
                </div>
            </figcaption>
        </figure>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="/posts.js"></script>
    <script type="text/javascript">
        $(function() {
            $(".hover").mouseleave(function() {
                $(this).removeClass("hover");
            });

            $("#refresh").on('click', function(e){
                e.preventDefault();
                posts.get();
            });

            posts.get();
        });
    </script>
</body>
</html>
