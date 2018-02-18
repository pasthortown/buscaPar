<?php
include_once('../controladores/Controlador_Base.php');
include_once('../entidades/CRUD/JugadoresJuego.php');
class Controlador_jugadoresjuego extends Controlador_Base
{
   function crear($args)
   {
      $jugadoresjuego = new JugadoresJuego($args["id"],$args["idJuego"],$args["idJugador"],$args["cartaJugador"],$args["seleccionJugador"],$args["momentoSeleccion"]);
      $cuantos = count($this->leer_filtrado(["columna"=>"idJuego", "tipo_filtro"=>"coincide", "filtro"=>$args["idJuego"]]));
      if($cuantos>=2){
            return false;
      }
      $sql = "INSERT INTO JugadoresJuego (idJuego,idJugador,cartaJugador,seleccionJugador,momentoSeleccion) VALUES (?,?,?,?,?);";
      $momentoSeleccionNoSQLTime = strtotime($jugadoresjuego->momentoSeleccion);
      $momentoSeleccionSQLTime = date("Y-m-d H:i:s", $momentoSeleccionNoSQLTime);
      $jugadoresjuego->momentoSeleccion = $momentoSeleccionSQLTime;
      $parametros = array($jugadoresjuego->idJuego,$jugadoresjuego->idJugador,$jugadoresjuego->cartaJugador,$jugadoresjuego->seleccionJugador,$jugadoresjuego->momentoSeleccion);
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      if(is_null($respuesta[0])){
         return true;
      }else{
         return false;
      }
   }

   function actualizar($args)
   {
      $jugadoresjuego = new JugadoresJuego($args["id"],$args["idJuego"],$args["idJugador"],$args["cartaJugador"],$args["seleccionJugador"],$args["momentoSeleccion"]);
      $fechaCreacion = date("Y-m-d H:i:s");
      $parametros = array($jugadoresjuego->idJuego,$jugadoresjuego->idJugador,$jugadoresjuego->cartaJugador,$jugadoresjuego->seleccionJugador,$fechaCreacion,$jugadoresjuego->id);
      $sql = "UPDATE JugadoresJuego SET idJuego = ?,idJugador = ?,cartaJugador = ?,seleccionJugador = ?,momentoSeleccion = ? WHERE id = ?;";
      $momentoSeleccionNoSQLTime = strtotime($jugadoresjuego->momentoSeleccion);
      $momentoSeleccionSQLTime = date("Y-m-d H:i:s", $momentoSeleccionNoSQLTime);
      $jugadoresjuego->momentoSeleccion = $momentoSeleccionSQLTime;
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
      $sql = "DELETE FROM JugadoresJuego WHERE id = ?;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      if(is_null($respuesta[0])){
         return true;
      }else{
         return false;
      }
   }

   function leer_especifico($args)
   {
      $idJuego = $args["idJuego"];
      $idJugador = $args["idJugador"];
      $parametros = [$idJuego, $idJugador];
      $sql = "SELECT * FROM JugadoresJuego WHERE idJuego = ? AND idJugador = ?;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function leer($args)
   {
      $id = $args["id"];
      if ($id==""){
         $sql = "SELECT * FROM JugadoresJuego;";
      }else{
      $parametros = array($id);
         $sql = "SELECT * FROM JugadoresJuego WHERE id = ?;";
      }
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function leer_paginado($args)
   {
      $pagina = $args["pagina"];
      $registrosPorPagina = $args["registros_por_pagina"];
      $desde = (($pagina-1)*$registrosPorPagina);
      $sql ="SELECT * FROM JugadoresJuego LIMIT $desde,$registrosPorPagina;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function numero_paginas($args)
   {
      $registrosPorPagina = $args["registros_por_pagina"];
      $sql ="SELECT IF(ceil(count(*)/$registrosPorPagina)>0,ceil(count(*)/$registrosPorPagina),1) as 'paginas' FROM JugadoresJuego;";
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
            $sql = "SELECT * FROM JugadoresJuego WHERE $nombreColumna = ?;";
            break;
         case "inicia":
            $sql = "SELECT * FROM JugadoresJuego WHERE $nombreColumna LIKE '$filtro%';";
            break;
         case "termina":
            $sql = "SELECT * FROM JugadoresJuego WHERE $nombreColumna LIKE '%$filtro';";
            break;
         default:
            $sql = "SELECT * FROM JugadoresJuego WHERE $nombreColumna LIKE '%$filtro%';";
            break;
      }
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }
}