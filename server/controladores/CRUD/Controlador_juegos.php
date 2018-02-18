<?php
include_once('../controladores/Controlador_Base.php');
include_once('../entidades/CRUD/Juegos.php');
class Controlador_juegos extends Controlador_Base
{
   function crear($args)
   {
      $juegos = new Juegos($args["id"],$args["fecha"],$args["idJugadorGanador"],$args["cartaMesa"]);
      $sql = "INSERT INTO Juegos (fecha,idJugadorGanador,cartaMesa) VALUES (?,?,?);";
      $fechaNoSQLTime = strtotime($juegos->fecha);
      $fechaSQLTime = date("Y-m-d H:i:s", $fechaNoSQLTime);
      $juegos->fecha = $fechaSQLTime;
      $parametros = array($juegos->fecha,$juegos->idJugadorGanador,$juegos->cartaMesa);
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      if(is_null($respuesta[0])){
         return true;
      }else{
         return false;
      }
   }

   function actualizar($args)
   {
      $juegos = new Juegos($args["id"],$args["fecha"],$args["idJugadorGanador"],$args["cartaMesa"]);
      $parametros = array($juegos->fecha,$juegos->idJugadorGanador,$juegos->cartaMesa,$juegos->id);
      $sql = "UPDATE Juegos SET fecha = ?,idJugadorGanador = ?,cartaMesa = ? WHERE id = ?;";
      $fechaNoSQLTime = strtotime($juegos->fecha);
      $fechaSQLTime = date("Y-m-d H:i:s", $fechaNoSQLTime);
      $juegos->fecha = $fechaSQLTime;
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
      $sql = "DELETE FROM Juegos WHERE id = ?;";
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
         $sql = "SELECT * FROM Juegos;";
      }else{
      $parametros = array($id);
         $sql = "SELECT * FROM Juegos WHERE id = ?;";
      }
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function leer_paginado($args)
   {
      $pagina = $args["pagina"];
      $registrosPorPagina = $args["registros_por_pagina"];
      $desde = (($pagina-1)*$registrosPorPagina);
      $sql ="SELECT * FROM Juegos LIMIT $desde,$registrosPorPagina;";
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }

   function numero_paginas($args)
   {
      $registrosPorPagina = $args["registros_por_pagina"];
      $sql ="SELECT IF(ceil(count(*)/$registrosPorPagina)>0,ceil(count(*)/$registrosPorPagina),1) as 'paginas' FROM Juegos;";
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
            $sql = "SELECT * FROM Juegos WHERE $nombreColumna = ?;";
            break;
         case "inicia":
            $sql = "SELECT * FROM Juegos WHERE $nombreColumna LIKE '$filtro%';";
            break;
         case "termina":
            $sql = "SELECT * FROM Juegos WHERE $nombreColumna LIKE '%$filtro';";
            break;
         default:
            $sql = "SELECT * FROM Juegos WHERE $nombreColumna LIKE '%$filtro%';";
            break;
      }
      $respuesta = $this->conexion->ejecutarConsulta($sql,$parametros);
      return $respuesta;
   }
}