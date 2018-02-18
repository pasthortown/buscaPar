var wsURL = 'http://localhost/Fijate/server/';
var CartaNueva = null;
var IdJuego = null;
var MomentoInicio = null;
var JugadoresNoListos = true;
var puntosJ1 = 10;
var puntosJ2 = 10;
var finRonda = false;
var seleccionoJugador1 = 0;
var seleccionoJugador2 = 0;
var momentoJugador1 = null;
var momentoJugador2 = null;
var idJugador1 = 0;
var idJugador2 = 0;
$(document).ready(function() {
    document.getElementById('siguienteCarta').style.display = "none";
    iniciar();
});

function iniciar() {
    urlToRequest = wsURL + 'cartas/iniciar';
    $.ajax({
        type: "post",
        url: urlToRequest,
        data: {},
        async:false,
        success: function(respuesta) {
            IdJuego = respuesta[0].id;        
            MomentoInicio = respuesta[0].fecha;
            document.getElementById('idJuego').innerHTML = IdJuego;
            document.getElementById('inicio').innerHTML = MomentoInicio;
            refrescar();
        }
    });
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

function refrescar() {
    if(JugadoresNoListos){
        setTimeout(() => {
            urlToRequest = wsURL + 'jugadoresjuego/leer_filtrado';
            $.ajax({
                type: "post",
                url: urlToRequest,
                data: {columna: "idJuego", tipo_filtro: "coincide", filtro: IdJuego},
                async:false,
                success: function(respuesta) {
                    cuenta = 0;
                    respuesta.forEach(element => {
                        cuenta++;
                        idJugador = element.idJugador;
                        getJugador(idJugador, cuenta);
                    });
                }
            });
            this.refrescar();
        }, 1000);
    }else{
        jugando();
    }
}

function getJugador(idJugador, cuenta) {
    urlToRequest = wsURL + 'usuarios/leer_filtrado';
    $.ajax({
        type: "post",
        url: urlToRequest,
        data: {columna: "id", tipo_filtro: "coincide", filtro: idJugador},
        async:false,
        success: function(respuesta) {
            if(cuenta == 1){
                if(respuesta[0]==0){
                    document.getElementById('jugador1').innerHTML = 'Esperando...';
                    document.getElementById('jugador2').innerHTML = 'Esperando...';
                }else {
                    document.getElementById('jugador1').innerHTML = respuesta[0].nombre;
                    idJugador1 = respuesta[0].id;
                    document.getElementById('jugador2').innerHTML = 'Esperando...';
                }
            }
            if(cuenta == 2){
                if(respuesta[0]==0){
                    document.getElementById('jugador2').innerHTML = 'Esperando...';
                }else {
                    document.getElementById('jugador2').innerHTML = respuesta[0].nombre;
                    idJugador2 = respuesta[0].id;
                }
                JugadoresNoListos = false;
            }
        }
    });
}

function comenzar() {
    puntosJ1 = 10;
    puntosJ2 = 10;
    finRonda = false;
    seleccionoJugador1 = 0;
    seleccionoJugador2 = 0;
    momentoJugador1 = null;
    momentoJugador2 = null;
    document.getElementById('seleccionJugador1').innerHTML = '';
    document.getElementById('seleccionJugador2').innerHTML = '';
    correctoJ1 = false;
    correctoJ2 = false;
    jugadorGanador = "Esperando...";
    momentoGanador = null;
    document.getElementById('nombreGanador').innerHTML = 'Esperando...';
    document.getElementById('momentoGanador').innerHTML = '';
    document.getElementById('siguienteCarta').style.display = "none";
    document.getElementById('puntosJ1').innerText = puntosJ1;
    document.getElementById('puntosJ2').innerText = puntosJ2;
    nuevaCarta();
}

function nuevaCarta() {
    urlToRequest = wsURL + 'cartas/siguiente';
    $.ajax({
        type: "post",
        url: urlToRequest,
        data: {IdJuego: IdJuego},
        async:false,
        success: function(respuesta) {
            CartaNueva = respuesta;
            mostrarCarta('Nueva Carta', CartaNueva);
            finRonda = false;
            seleccionoJugador1 = 0;
            seleccionoJugador2 = 0;
            momentoJugador1 = null;
            momentoJugador2 = null;
            document.getElementById('seleccionJugador1').innerHTML = '';
            document.getElementById('seleccionJugador2').innerHTML = '';
            correctoJ1 = false;
            correctoJ2 = false;
            jugadorGanador = "Esperando...";
            momentoGanador = null;
            document.getElementById('nombreGanador').innerHTML = 'Esperando...';
            document.getElementById('momentoGanador').innerHTML = '';
            document.getElementById('siguienteCarta').style.display = "none";
            jugando();
        }
    });
}

function jugando() {
    if(!finRonda){
        setTimeout(() => {
            urlToRequest = wsURL + 'jugadoresjuego/leer_filtrado';
            $.ajax({
                type: "post",
                url: urlToRequest,
                data: {columna: "idJuego", tipo_filtro: "coincide", filtro: IdJuego},
                async:false,
                success: function(respuesta) {
                    cuenta = 0;
                    respuesta.forEach(element => {
                        cuenta++;
                        momento = element.momentoSeleccion;
                        simbolo = element.seleccionJugador;
                        mostrarSeleccion(simbolo, momento, cuenta);
                    });
                }
            });
            this.jugando();
        }, 1000);
    }
}

function mostrarSeleccion(simbolo, momento, cuenta) {
    contenido = '<div class="form-group row"><div class="col-3"></div><div class="col-3 text-center"><img src="./../images/'+simbolo+'.png" class="simboloCarta"></div></div>';
    contenido += '<div class="col-12 text-center"><strong>'+momento+'</strong></div>';
    if(simbolo == 0){
        return;
    }
    if(cuenta == 1){
        document.getElementById('seleccionJugador1').innerHTML = contenido;
        seleccionoJugador1 = simbolo;
        momentoJugador1 = momento;
    }
    if(cuenta == 2){    
        document.getElementById('seleccionJugador2').innerHTML = contenido;
        seleccionoJugador2 = simbolo;
        momentoJugador2 = momento;
    }
    if(seleccionoJugador1>0 && seleccionoJugador2>0 && !finRonda){
        finRonda = true;
        correctoJ1 = false;
        correctoJ2 = false;
        jugadorGanador = "Esperando...";
        momentoGanador = null;
        CartaNueva.forEach(simbolo => {
            if(simbolo==seleccionoJugador1) {
                correctoJ1 = true;
            };
        });
        CartaNueva.forEach(simbolo => {
            if(simbolo==seleccionoJugador2) {
                correctoJ2 = true;
            };
        });
        if(correctoJ1 && correctoJ2){
            if(momentoJugador1>momentoJugador2){
                momentoGanador = momentoJugador2;
                if(puntosJ1>0){
                    puntosJ1--;
                }
                jugadorGanador = document.getElementById('jugador2').innerHTML;
            }else{
                momentoGanador = momentoJugador1;
                if(puntosJ2>0){
                    puntosJ2--;
                }
                jugadorGanador = document.getElementById('jugador1').innerHTML;
            }
        }
        if(correctoJ1 && !correctoJ2){
            momentoGanador = momentoJugador1;
            if(puntosJ2>0){
                puntosJ2--;
            }
            jugadorGanador = document.getElementById('jugador1').innerHTML;
        }
        if(!correctoJ1 && correctoJ2){
            momentoGanador = momentoJugador2;
            if(puntosJ1>0){
                puntosJ1--;
            }
            jugadorGanador = document.getElementById('jugador2').innerHTML;
        }
        if(!correctoJ1 && !correctoJ2){
            momentoGanador = "";
            jugadorGanador = "No hay ganador!!!";
        }
        if(puntosJ1 == 0){
            ganador(idJugador1);
            swal({
                title: "Fin del Juego",
                text: "El ganador es " + document.getElementById('jugador2').innerHTML,
                icon: "success",
            })
            .then((r)=>{
                location.reload(true);
            });
        }
        if(puntosJ2 == 0){
            ganador(idJugador2);
            swal({
                title: "Fin del Juego",
                text: "El ganador es " + document.getElementById('jugador1').innerHTML,
                icon: "success",
            })
            .then((r)=>{
                location.reload(true);
            });
        }
        document.getElementById('nombreGanador').innerHTML = jugadorGanador;
        document.getElementById('momentoGanador').innerHTML = momentoGanador;
        document.getElementById('puntosJ1').innerText = puntosJ1;
        document.getElementById('puntosJ2').innerText = puntosJ2;
        document.getElementById('siguienteCarta').style.display = "block";
    }
}

function ganador(idJugadorGanador) {
    urlToRequest = wsURL + 'juegos/actualizar';
    $.ajax({
        type: "post",
        url: urlToRequest,
        data: {id: IdJuego, fecha: MomentoInicio, idJugadorGanador: idJugadorGanador, cartaMesa: ""},
        async:false,
        success: function(respuesta) {
            
        }
    });
}