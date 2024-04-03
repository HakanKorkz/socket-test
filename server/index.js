import { createServer } from "node:http";
import { Server } from "socket.io";

const httpServer = createServer();
const io = new Server(httpServer,{
    cors: {
        origin: "*"
    }
});

io.on("connection", (socket) => {
    console.log("Bir kullanıcı bağlandı",socket.id);

    socket.on("client", (arg) => {
        io.emit("client", arg);
    })

    socket.on("spin",(spin)=>{
        console.log(spin)
        io.emit("spin",spin)
    })

    socket.on("disconnect", () => {
        console.log("Bir kullanıcı ayrıldı");
    });
});

httpServer.listen(5000);
