<?php
class Juegos
{
   public $id;
   public $fecha;
   public $idJugadorGanador;
   public $cartaMesa;

   function __construct($id,$fecha,$idJugadorGanador,$cartaMesa){
      $this->id = $id;
      $this->fecha = $fecha;
      $this->idJugadorGanador = $idJugadorGanador;
      $this->cartaMesa = $cartaMesa;
   }
}
?>