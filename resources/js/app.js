import './bootstrap';
// resources/js/app.js

if ('Notification' in window) {
    Notification.requestPermission();
}

Echo.channel('tickets')
    .listen('.broadcast', function (data) {
        new Notification(data.title, {
            body: data.body,
        });
    });
