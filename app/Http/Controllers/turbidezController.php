<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Rango;


use App\Models\Tinaco;
use App\Models\SensorTinaco;
use Illuminate\Support\Facades\Auth;

class turbidezController extends Controller
{
    protected $adafruitService;
    //falta poner bd

public function __construct(AdafruitService $adafruitService)
{
    $this->adafruitService = $adafruitService;
}


public function obtenerturbidez( Request $request)
{
    $usuario = Auth::user();
        $tinacoId = $request->input('tinaco_id');
        $tinaco = Tinaco::find($tinacoId);

        $sensorTinaco = SensorTinaco::where('tinaco_id', $tinaco->id)
        ->join('sensor', 'sensor_tinaco.sensor_id', '=', 'sensor.id')
        ->where('sensor.nombre', 'Turbidez') 
        ->first();

        if (!$sensorTinaco) {
            return response()->json(['mensaje' => 'Sensor de ph no encontrado para el tinaco especificado'], 404);
        }
        $sensor = $sensorTinaco->sensor;

    $data = $this->adafruitService->getFeedData("turbidez");
    $this->guardarDatos($sensorTinaco,$tinaco,$data, $sensor, $usuario);

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
        
    public function guardarDatos($sensorTinaco,$tinaco,$data, $sensor, $usuario)
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
      /*   $sensor = Sensor::firstOrCreate([
            'nombre' => 'Turbidez',
            "modelo" => "Sensor de turbidez con salida analógica y digital",
            "unidad_medida" => "ms",
        
        
        ]); */
      /*   $rango = Rango::firstOrCreate([
            'rango_min' => 0,
            'rango_max' => 1000,
           
        ]); */

        
        $Valor = Valor::create([
            'value' => $valor,
            'id_sensor'=> 4

        ]);
       // $sensor->save();
       $sensorTinaco->id_valor = $Valor->id;
       $Valor->save();

       $sensorTinaco->save();
    }
}
