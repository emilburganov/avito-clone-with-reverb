<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>
<body>
<script type="module">
    const appKey = "vkn2tp15odh078vv0y4j";
    const socket = new WebSocket(`ws://localhost:8080/app/${appKey}`);

    const userToken = "e919a6f3-ed28-44bf-89ef-cb5980f7fd7b";
    const userId = 1;

    const channel = "ad-featured";
    const channelName = `private-${channel}.${userId}`;

    const getAuth = async (socketId) => {
        const response = await fetch("http://localhost:8000/broadcasting/auth", {
            headers: {
                "Authorization": `Bearer ${userToken}`,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                socket_id: socketId,
                channel_name: channelName,
            }),
            method: "POST",
        });

        if (!response.ok) {
            alert("Ошибка при авторизации!");
        }

        return (await response.json()).auth;
    }

    socket.onopen = async function () {
        console.log("Соединение установлено.");
    };

    socket.onmessage = async function (event) {
        console.log("Данные получены с сервера.");

        const eventData = JSON.parse(event?.data);

        if (!eventData?.data) {
            return;
        }

        const data = JSON.parse(eventData.data);

        console.log(data);

        const socketId = data?.socket_id;

        if (socketId) {
            const auth = await getAuth(socketId);

            await socket.send(JSON.stringify({
                event: "pusher:subscribe",
                data: {
                    channel: channelName,
                    auth: auth
                }
            }));
        }
    };

    socket.onclose = function (event) {
        if (event.wasClean) {
            console.log(`Соединение закрыто чисто, код=${event.code} причина=${event.reason}.`);
        } else {
            console.log("Соединение прервано.");
        }
    };

    socket.onerror = function () {
        console.log("Ошибка!");
    };
</script>
</body>
</html>
