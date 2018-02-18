var wsURL = 'http://192.168.1.102/Fijate/server/';
var CartaNueva = null;
var CartaJugador = null;
var Seleccionable = true;

$(document).ready(function() {
    document.getElementById('usuarioAutenticado').innerHTML=JSON.parse(sessionStorage.getItem('usuario')).nombre;
    document.getElementById('idJuego').innerHTML=JSON.parse(sessionStorage.getItem('JugadorJuego')).idJuego;
    sessionStorage.setItem('cartaMesa','');
    jugando();
});

function jugando() {
    setTimeout(() => {
        IdJuego = document.getElementById('idJuego').innerHTML;
        urlToRequest = wsURL + 'juegos/leer_filtrado';
        $.ajax({
            type: "post",
            url: urlToRequest,
            data: {columna: "id", tipo_filtro: "coincide", filtro: IdJuego},
            async:false,
            success: function(juegoResult) {
                CartaNueva = juegoResult[0].cartaMesa;
                if(juegoResult[0].idJugadorGanador>0){
                    if(juegoResult[0].idJugadorGanador == JSON.parse(sessionStorage.getItem('usuario')).id){
                        swal({
                            title: "Felicidades",
                            text: "Eres ganaste!!!",
                            icon: "success",
                        })
                        .then((r)=>{
                            salir();
                        });
                    }else{
                        swal({
                            title: "Fin del Juego",
                            text: "Perdiste, vuelve a intentarlo!!!",
                            icon: "error",
                        })
                        .then((r)=>{
                            salir();
                        });
                    }
                }
                mostrarCartaMesa("Carta Nueva",JSON.parse(CartaNueva));
                urlToRequest = wsURL + 'jugadoresjuego/leer_filtrado';
                $.ajax({
                    type: "post",
                    url: urlToRequest,
                    data: {columna: "idJuego", tipo_filtro: "coincide", filtro: IdJuego},
                    async:false,
                    success: function(respuesta) {
                        respuesta.forEach(element => {
                            if(element.idJugador == JSON.parse(sessionStorage.getItem('usuario')).id){
                                if(element.seleccionJugador == 0){
                                    Seleccionable=true;
                                }else{
                                    document.getElementById(element.seleccionJugador).style.border= "2px solid red";
                                    Seleccionable=false;
                                }
                                if(Seleccionable){
                                    CartaJugador = JSON.parse(element.cartaJugador);
                                    mostrarCarta(JSON.parse(sessionStorage.getItem('usuario')).nombre,CartaJugador);
                                }
                            }
                        });
                    }
                });     
            }
        });
        this.jugando();
    }, 1000);
}

function mostrarCartaMesa(jugador,simbolos) {
    var contenido = '';
    contenido += '<div class="col-12"><div class="card"><div class="card-body"><h5 class="card-title">' + jugador + '</h5>';
    contenido += '<div class="form-group row">';
    simbolos.forEach(simbolo => {
        contenido += '<div class="col-3 text-center"><img src="./../images/' + simbolo + '.png" class="simboloCarta"></div>';
    });
    contenido += '</div></div></div></div>';
    document.getElementById('cartaMesa').innerHTML = contenido;
}

function mostrarCarta(jugador,simbolos) {
    var contenido = '';
    contenido += '<div class="col-12"><div class="card"><div class="card-body"><h5 class="card-title">' + jugador + '</h5>';
    contenido += '<div class="form-group row">';
    simbolos.forEach(simbolo => {
        contenido += '<div class="col-3 text-center" id="' + simbolo + '"><img src="./../images/' + simbolo + '.png" class="simboloCarta" onclick="selecciona(' + simbolo + ')"></div>';
    });
    contenido += '</div></div></div></div>';
    document.getElementById('cartas').innerHTML = contenido;
}

function salir() {
    sessionStorage.clear();
    window.location = './../index.html';
}

function selecciona(simbolo) {
    if(Seleccionable){
        urlToRequest = wsURL + 'jugadoresjuego/actualizar';
        jugadorActual = JSON.parse(sessionStorage.getItem('JugadorJuego'));
        jugadorActual.seleccionJugador = simbolo;
        var d = new Date();
        jugadorActual.momentoSeleccion = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
        sessionStorage.setItem('JugadorJuego',JSON.stringify(jugadorActual));
        $.ajax({
            type: "post",
            url: urlToRequest,
            data: JSON.stringify(jugadorActual),
            async:false,
            success: function(respuesta) {
                
            }
        });
        document.getElementById(simbolo).style.border= "2px solid red";
        Seleccionable=false;
    }
}