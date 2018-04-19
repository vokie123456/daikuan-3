<!DOCTYPE html>
<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('0918d2c5c8981ec5e1a1', {
            cluster: 'ap1',
            encrypted: true
        });
        
        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            console.log(data);
            alert(data.message);
        });
    </script>
</head>

<body>
</body>