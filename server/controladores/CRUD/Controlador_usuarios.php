<?php
include_once('../controladores/Controlador_Base.php');
include_once('../entidades/CRUD/Usuarios.php');
include_once('../controladores/CRUD/Controlador_jugadoresjuego.php');
include_once('../controladores/CRUD/Controlador_juegos.php');
include_once('../controladores/especificos/Controlador_cartas.php');
class Controlador_usuarios extends Controlador_Base
{
   function entrar($args)
   {
      $controladorJuegos = new Controlador_juegos();
      $controladorJugadoresJuego = new Controlador_jugadoresjuego();
      $ControladorCartas = new Controlador_cartas();
      $nombre = $args["nombre"];
      $idJuego = $args["idJuego"];
      $filtro = ["columna"=>"nombre", "tipo_filtro"=>"coincide", "filtro"=>$nombre];
      if ($this->leer_filtrado($filtro)[0]==0){
         $this->crear($args);
      }
      $jugador = $this->leer_filtrado($filtro)[0];
      $fechaIngreso = date("Y-m-d H:i:s");
      $juego = $controladorJuegos->leer(["id"=>$idJuego])[0];
      $cartaTablero = json_decode($juego["cartaMesa"]);
      $cartaJugador = $ControladorCartas->carta_nueva_jugador(["cartaTablero"=>$cartaTablero]);
      $acceso = $controladorJugadoresJuego->crear(["id"=>0,"idJuego"=>$idJuego, "idJugador"=>$jugador["id"], "cartaJugador"=>json_encode($cartaJugador), "seleccionJugador"=>0, "momentoSeleccion"=>$fechaIngreso]);
      if($acceso){
            $jugadorJuego = $controladorJugadoresJuego->leer_especifico(["idJuego"=>$idJuego, "idJugador"=>$jugador["id"]])[0];
            return ["jugador"=>$jugador,"JugadorJuego"=>$jugadorJuego, "CartaMesa"=>json_encode($juego["cartaMesa"])];
      }else{
            return false;
      }
      
   }

   function crear($args)
   {
      $usuarios = new Usuarios($args["id"],$args["nombre"]);
      $sql = "INSERT INTO Usuarios (nombre) VALUES (?);";
      $parametros = array($usuarios->nombre);
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      if(is_null($respuesta[0])){
         return true;
      }else{
         return false;
      }
   }

   function actualizar($args)
   {
      $usuarios = new Usuarios($args["id"],$args["nombre"]);
      $parametros = array($usuarios->nombre,$usuarios->id);
      $sql = "UPDATE Usuarios SET nombre = ? WHERE id = ?;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      if(is_null($respuesta[0])){
         return true;
      }else{
         return false;
      }
   }

   function borrar($args)
   {
      $id = $args["id"];
      $parametros = array($id);
      $sql = "DELETE FROM Usuarios WHERE id = ?;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      if(is_null($respuesta[0])){
         return true;
      }else{
         return false;
      }
   }

   function leer($args)
   {
      $id = $args["id"];
      if ($id==""){
         $sql = "SELECT * FROM Usuarios;";
      }else{
      $parametros = array($id);
         $sql = "SELECT * FROM Usuarios WHERE id = ?;";
      }
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function leer_paginado($args)
   {
      $pagina = $args["pagina"];
      $registrosPorPagina = $args["registros_por_pagina"];
      $desde = (($pagina-1)*$registrosPorPagina);
      $sql ="SELECT * FROM Usuarios LIMIT $desde,$registrosPorPagina;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function numero_paginas($args)
   {
      $registrosPorPagina = $args["registros_por_pagina"];
      $sql ="SELECT IF(ceil(count(*)/$registrosPorPagina)>0,ceil(count(*)/$registrosPorPagina),1) as 'paginas' FROM Usuarios;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta[0];
   }

   function leer_filtrado($args)
   {
      $nombreColumna = $args["columna"];
      $tipoFiltro = $args["tipo_filtro"];
      $filtro = $args["filtro"];
      switch ($tipoFiltro){
         case "coincide":
            $parametros = array($filtro);
            $sql = "SELECT * FROM Usuarios WHERE $nombreColumna = ?;";
            break;
         case "inicia":
            $sql = "SELECT * FROM Usuarios WHERE $nombreColumna LIKE '$filtro%';";
            break;
         case "termina":
            $sql = "SELECT * FROM Usuarios WHERE $nombreColumna LIKE '%$filtro';";
            break;
         default:
            $sql = "SELECT * FROM Usuarios WHERE $nombreColumna LIKE '%$filtro%';";
            break;
      }
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }
}