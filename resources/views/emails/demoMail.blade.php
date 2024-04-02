<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webappfix</title>
</head>
<body>
    <h1>{{ $mailData['title'] }}</h1>
    <br>
    <h2>Dear {{$mailData['buyer_name']}},</h2>
    <br>
    @if ($mailData['body_3'] !== ' has been accepted.')
        <h3 style="color: rgb(252, 31, 31)">{{$mailData['body_1']}}<a href="http://localhost:4200/offered-properties" style="color: rgb(41, 41, 253)">{{$mailData['body_2']}}</a>{{$mailData['body_3']}}</h3>
    @else
        <h3 style="color: rgb(48, 179, 4)">{{$mailData['body_1']}}<a href="http://localhost:4200/offered-properties" style="color: rgb(41, 41, 253)">{{$mailData['body_2']}}</a>{{$mailData['body_3']}}</h3>
        <br>
        <a style="display: block; font-size: 17px;" href="http://localhost:4200/offered-properties">Click here to complete payment</a>
        <br>
    @endif
    <br>
    <img style="width: 100%;" src="https://s7d9.scene7.com/is/image/ledcor/Belmont%20Reunion%2003?qlt=85&wid=480&ts=1691085180535&dpr=on,2.625">
    <br>
    <h4>Thank You.</h4>

</body>
</html>