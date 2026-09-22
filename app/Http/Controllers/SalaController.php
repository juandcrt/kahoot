<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaJuego;

class SalaController extends Controller
{
    public function destroy($id)
    {
        $sala = SalaJuego::findOrFail($id);
        $sala->delete();

        return redirect()->back()->with('success', 'Sala eliminada correctamente.');
    }
}
