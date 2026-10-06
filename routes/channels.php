<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Agrega esto para las salas del juego:
Broadcast::channel('game.{gameId}', function ($user, $gameId) {
    // Aquí puedes retornar true temporalmente para pruebas, 
    // o validar si el usuario forma parte de la partida.
    return true; 
});