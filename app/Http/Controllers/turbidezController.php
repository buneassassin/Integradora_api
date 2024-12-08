<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Rango;
class turbidezController extends Controller
{
    protected $adafruitService;
    //falta poner bd

public function __construct(AdafruitService $adafruitService)
{
    $this->adafruitService = $adafruitService;
}


public function obtenerturbidez()
{
    $data = $this->adafruitService->getFeedData("turbidez");
    $this->guardarDatos($data);

    $mensaje = $this->significadoDatos($data);

    return response()->json(['mensaje' => $mensaje]);



    


    
}

    public function significadodatos($data)
    {
        
        $data = is_string($data) ? json_decode($data) : $data;
        
        $valor = $data['last_value'] ?? null;

        if (is_null($valor)) 
        {


            return "No hay datos de turbidez dispoibles para calcular";

            
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;
    
        if (is_null($valor)) {
            return "El valor de temperatura no es numérico";
        }
        if ($valor >= 0 || $valor <= 1) 
        {

            return "Potable ideal: {$valor}";
        } 
        else if ($valor >= 1 || $valor <= 5) 
        {
            return "Potable: {$valor}";
        } 
        else if ($valor >= 5 || $valor <= 50)
         {
            return "Para lavar: {$valor}";
         }
         else if ($valor >= 50 || $valor <= 100)
         {
            return "Ligeramente sucia: {$valor}";
         }
         else if ($valor >= 100 || $valor <= 1000)
         {
            return "No usar, muy sucia: {$valor}";
         }
         else if ($valor >= 1000)
         {
            return "Aguas negras: {$valor}";
         }
  

        return "turbidez fuera de rango: {$valor}";
    }
        




        //switch ($data->data->value) 
        
           

           // case 0:
             //   return "Temperatura baja";
                //break;
        
    public function guardarDatos($data)
    {
        $data = is_string($data) ? json_decode($data) : $data;
        
        $valor = $data['last_value'] ?? null;

        if (is_null($valor)) 
        {


            return "Datos de turbidez no disponibles";

            
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;
        
        
        //por si no habia
        $sensor = Sensor::firstOrCreate([
            'nombre' => 'Turbidez',
            "modelo" => "Sensor de turbidez con salida analógica y digital",
            "unidad_medida" => "ms",
        
        
        ]);
        $rango = Rango::firstOrCreate([
            'rango_min' => 0,
            'rango_max' => 1000,
           
        ]);

        $Valor = Valor::create([
            'id_sensor' => $sensor->id,
            "id_rango" => $rango->id,
            'value' => $valor,
            'unidad' => 'ph',
        ]);

        $sensor->save();
        $Valor->save();
    }
}
