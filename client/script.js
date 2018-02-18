var wsURL = 'http://localhost/Fijate/server/';

$(document).ready(function(){
});

function entrar(){
    nombre = document.getElementById('nickname').value;
    idJuego = document.getElementById('idJuego').value;
    urlToRequest = wsURL + 'usuarios/entrar';
    $.ajax({
        type: "post",
        url: urlToRequest,
        data: {nombre: nombre, idJuego: idJuego},
        async:false,
        success: function(respuesta){
            if(respuesta == false){
                swal({
                    title: "Juego Lleno",
                    text: "El Juego al que deseas acceder se encuentra lleno.",
                    icon: "error",
                })
                .then((r)=>{
                });
            }else{
                sessionStorage.setItem('usuario',JSON.stringify(respuesta.jugador));
                sessionStorage.setItem('JugadorJuego',JSON.stringify(respuesta.JugadorJuego));
                swal({
                    title: "Bienvenido!",
                    text: "Usted ha sido identificado como: " + respuesta.jugador.nombre,
                    icon: "success",
                })
                .then((r)=>{
                    window.location = './juego/';
                });
            }
        }
    });
}