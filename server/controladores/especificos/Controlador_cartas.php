<?php
include_once('../controladores/Controlador_Base.php');
include_once('../controladores/CRUD/Controlador_juegos.php');
include_once('../controladores/CRUD/Controlador_jugadoresjuego.php');
class Controlador_cartas extends Controlador_Base
{
    public $simbolos;

    function __construct(){
        $this->simbolos = $this->generar_simbolos(36);
    }

    private function generar_simbolos($max){
        $toReturn = [];
        for($i=1 ; $i<=$max ; $i++){
        array_push($toReturn, $i);
        }
        return $toReturn;
    }

    public function iniciar(){
        $juegos = new Controlador_juegos();
        $fechaCreacion = date("Y-m-d H:i:s");
        $juegos->crear(["id"=>0,"fecha"=>$fechaCreacion, "idJugadorGanador"=>0, "cartaMesa"=>""]);
        $juegoCreado = $juegos->leer_filtrado(["columna"=>"fecha","tipo_filtro"=>"coincide", "filtro"=>$fechaCreacion]);
        return $juegoCreado;
    }

    public function siguiente($args){
        $idJuego = $args['IdJuego'];
        $controlador_juegos = new Controlador_juegos();
        $controlador_jugadoresJuego = new Controlador_jugadoresjuego();
        shuffle($this->simbolos);
        $cartaMesa = [$this->simbolos[0],
                      $this->simbolos[1],
                      $this->simbolos[2],
                      $this->simbolos[3],
                      $this->simbolos[4],
                      $this->simbolos[5],
                      $this->simbolos[6],
                      $this->simbolos[7],
                      ];
        $cartaJugador1 = [$this->simbolos[0],
                          $this->simbolos[8],
                          $this->simbolos[9],
                          $this->simbolos[10],
                          $this->simbolos[11],
                          $this->simbolos[12],
                          $this->simbolos[13],
                          $this->simbolos[14],
                          ]; 
        $cartaJugador2 = [$this->simbolos[7],
                          $this->simbolos[8],
                          $this->simbolos[15],
                          $this->simbolos[16],
                          $this->simbolos[17],
                          $this->simbolos[18],
                          $this->simbolos[19],
                          $this->simbolos[20],
                          ]; 
        shuffle($cartaJugador1);
        shuffle($cartaJugador2);
        shuffle($cartaMesa);
        $juego = $controlador_juegos->leer_filtrado(["columna"=>"id", "tipo_filtro"=>"coincide", "filtro"=>$idJuego]);
        $jugadoresJuego = $controlador_jugadoresJuego->leer_filtrado(["columna"=>"idJuego", "tipo_filtro"=>"coincide", "filtro"=>$idJuego]);
        $juego[0]["cartaMesa"]=json_encode($cartaMesa);
        $jugadoresJuego[0]["cartaJugador"]=json_encode($cartaJugador1);
        $jugadoresJuego[1]["cartaJugador"]=json_encode($cartaJugador2);
        $controlador_juegos->actualizar(["id"=>$juego[0]["id"],"fecha"=>$juego[0]["fecha"],"idJugadorGanador"=>0,"cartaMesa"=>$juego[0]["cartaMesa"]]);
        $fechaCreacion = date("Y-m-d H:i:s");
        $controlador_jugadoresJuego->actualizar(["id"=>$jugadoresJuego[0]["id"], "idJuego"=>$jugadoresJuego[0]["idJuego"],"idJugador"=>$jugadoresJuego[0]["idJugador"],"cartaJugador"=>$jugadoresJuego[0]["cartaJugador"],"seleccionJugador"=>0,"momentoSeleccion"=>$fechaCreacion]);
        $controlador_jugadoresJuego->actualizar(["id"=>$jugadoresJuego[1]["id"], "idJuego"=>$jugadoresJuego[1]["idJuego"],"idJugador"=>$jugadoresJuego[1]["idJugador"],"cartaJugador"=>$jugadoresJuego[1]["cartaJugador"],"seleccionJugador"=>0,"momentoSeleccion"=>$fechaCreacion]);
        return $cartaMesa;
    }

    public function carta_nueva_jugador($args) {
        $cartaTablero = $args['cartaTablero'];
        shuffle($cartaTablero);
        $toreturn = [];
        array_push($toreturn,$cartaTablero[0]);
        $cuenta = 1;
        while($cuenta<8) {
            shuffle($this->simbolos);
            $noExiste = $this->existe($cartaTablero, $this->simbolos[0]);
            if($noExiste){
                $noExiste = $this->existe($toreturn, $this->simbolos[0]);
            }
            if($noExiste){
                array_push($toreturn,$this->simbolos[0]);
                $cuenta++;
            }
        }
        shuffle($toreturn);
        return $toreturn;
    }

    public function carta_nueva_tablero($args) {
        $cartaJugador1 = $args['cartaJugador1'];
        $cartaJugador2 = $args['cartaJugador2'];
        shuffle($cartaJugador1);
        shuffle($cartaJugador2);
        $toreturn = [];
        array_push($toreturn,$cartaJugador1[0]);
        array_push($toreturn,$cartaJugador2[0]);
        $cuenta = 2;
        while($cuenta<8) {
            shuffle($this->simbolos);
            $noExiste = $this->existe($cartaJugador1, $this->simbolos[0]);
            if($noExiste){
                $noExiste = $this->existe($cartaJugador2, $this->simbolos[0]);
            }
            if($noExiste){
                $noExiste = $this->existe($toreturn, $this->simbolos[0]);
            }
            if($noExiste){
                array_push($toreturn,$this->simbolos[0]);
                $cuenta++;
            }
        }
        shuffle($toreturn);
        return $toreturn;
    }

    private function existe($cuerpo, $elemento){
        $toreturn = true;
        for($i=0; $i<=count($cuerpo); $i++) {
            if($cuerpo[$i] == $elemento){
                $toreturn = false;
            }
        }
        return $toreturn;
    }
}