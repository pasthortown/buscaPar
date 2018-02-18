<?php
class JugadoresJuego
{
   public $id;
   public $idJuego;
   public $idJugador;
   public $cartaJugador;
   public $seleccionJugador;
   public $momentoSeleccion;

   function __construct($id,$idJuego,$idJugador,$cartaJugador,$seleccionJugador,$momentoSeleccion){
      $this->id = $id;
      $this->idJuego = $idJuego;
      $this->idJugador = $idJugador;
      $this->cartaJugador = $cartaJugador;
      $this->seleccionJugador = $seleccionJugador;
      $this->momentoSeleccion = $momentoSeleccion;
   }
}
?>