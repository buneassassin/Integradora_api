<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;

class encenderbombaController extends Controller
{
    protected $adafruitService;
    //falta poner bd

        public function __construct(AdafruitService $adafruitService)
        {
            $this->adafruitService = $adafruitService;
        }
        public function encenderbomba()
        {
            $exito = $this->adafruitService->enviardatos("botonsito",1);

            $mensaje = $exito ? "Bomba prendida." : "Error al enviar el dato.";

    
            return response()->json(['mensaje' => $mensaje]);
    
        }
        public function apagarbomba()
        {
            $exito = $this->adafruitService->enviardatos("botonsito",0);

            $mensaje = $exito ? "Bomba apagada." : "Error al enviar el dato.";

    
            return response()->json(['mensaje' => $mensaje]);
    
        }
}
