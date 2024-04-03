<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Socket-test / client</title>
    <script src="https://cdn.tailwindcss.com/3.4.3"></script>

</head>
<body>

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
             alt="Your Company">
        <div class="text-3xl font-bold tracking-tight text-gray-900 w-full text-center" id="spin">
            0
        </div>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" method="POST">
            <div>
                <label for="text" class="block text-sm font-medium leading-6 text-gray-900">Değer giriniz</label>
                <div class="mt-2">
                    <input id="text" name="text" type="text" required
                           class="block w-full rounded-md border-0 py-1.5 px-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div>
                <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Yolla
                </button>
            </div>
        </form>
    </div>

    <ul role="list" class="divide-y divide-gray-100" id="list">
        <li class="flex justify-between gap-x-6 py-5">
            <div class="flex min-w-0 gap-x-4">
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50"
                     src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                     alt="">
                <div class="min-w-0 flex-auto">
                    <p class="text-sm font-semibold leading-6 text-gray-900">Leslie Alexander</p>
                </div>
            </div>
        </li>
    </ul>

</div>

<div id="dot" style="position: absolute; font-size: 72px; color: red; left: 0; top: 50%;
pointer-events: none">
    .
</div>

<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>

    const textInput = document.getElementById("text");
    const formElement = document.querySelector("form");

    const socket = io("ws://localhost:3000");

    formElement.addEventListener("submit", (e) => {
        e.preventDefault();
        fetch("/client/elephant-io", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({text: textInput.value})
        }).then(res => res.json()).then(data => {
            console.log(data)
        }).catch(er=>console.log(er))

    })


    // socket.on("connect", () => {
    //     console.log("Bağlantı kuruldu: ", socket.id);
    //     console.log("socket connected: ", socket.connected);
    //     socket.on("client", (args) => {
    //         dataList(args)
    //     })
    //     const engine = socket.io.engine;
    //     formElement.addEventListener("submit", (e) => {
    //         e.preventDefault();
    //         socket.emit("client", textInput.value);
    //         engine.on("packetCreate", ({ type, data }) => {
    //             // called for each packet sent
    //             console.log(type, data);
    //         })
    //         socket.on("client", (args) => {
    //             dataList(args)
    //         })
    //
    //       //  engine.transport.close();
    //     })
    //
    // })
    //
    // socket.on("disconnect", () => {
    //     console.log("socket disconnected: ", socket.connected);
    //     console.log("Bağlantı kesildi: ", socket.id);
    // });

    socket.on("client", (args) => {
        dataList(args)
    })

    // formElement.addEventListener("submit", (e) => {
    //     e.preventDefault();
    //     socket.emit("client", [{
    //         id: 1,
    //         title: textInput.value,
    //         image: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
    //     }]);
    //     engine.on("packetCreate", ({type, data}) => {
    //         // called for each packet sent
    //         console.log(type, data);
    //     })
    //     socket.on("client", (args) => {
    //         dataList(args)
    //     })
    //
    //     //  engine.transport.close();
    // })

    const dataList = (data) => {
        const list = document.getElementById("list")
        console.log(data)
        data.map(dt => {
            list.innerHTML += `  <li class="flex justify-between gap-x-6 py-5" data-key="${dt.id}">
            <div class="flex min-w-0 gap-x-4">
                <img class="h-12 w-12 flex-none rounded-full bg-gray-50" src="${dt.image}" alt="">
                <div class="min-w-0 flex-auto">
                    <p class="text-sm font-semibold leading-6 text-gray-900">${dt.title}</p>
                </div>
            </div>
        </li>`
        })

    }

    // const html = document.querySelector("body");
    //
    // html.addEventListener("mouseover", (e) => {
    //     console.log(e)
    //     socket.emit("spin", e.clientX)
    // })

    document.addEventListener("mousemove", (event) => {
        const x = event.clientX;
        const y = event.clientY;

        socket.emit("spin", { x, y });
    });

    socket.on("spin", (data) => {
        const dotElement = document.getElementById("dot");
        const spinlement = document.getElementById("spin");
        dotElement.style.left = `${data.x}px`;
        dotElement.style.top = `${data.y}px`;
        spinlement.innerText = data.x+data.y;
    });

    // socket.on("spin", (args) => {
    //     console.log(args)
    //     document.getElementById("spin").innerHTML = args
    //
    // })
</script>
</body>
</html>