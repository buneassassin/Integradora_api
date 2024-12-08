<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Rango;
class TDSController extends Controller
{
    protected $adafruitService;

public function __construct(AdafruitService $adafruitService)
{
    $this->adafruitService = $adafruitService;
}


public function obtenerturbidez()
{
    $data = $this->adafruitService->getFeedData("tds");
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


            return "No hay datos de TDS dispoibles para calcular";

            
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;
    
        if (is_null($valor)) {
            return "El valor de TDS no es numérico";
        }
        if ($valor >= 0 || $valor <= 50) 
        {

            return "Destiliazada o desmineralizada: {$valor}";
        } 
        else if ($valor >= 50 || $valor <= 500) 
        {
            return "Potable,apta para el consumo humano: {$valor}";
        } 
        else if ($valor >= 500 || $valor <= 2000)
         {
            return "De industria, no apta para consumo: {$valor}";
         }
      
         else if ($valor >= 2000)
         {
            return "Aguas salada: {$valor}";
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


            return "Datos de tds no disponibles";

            
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;
        
        
        //por si no habia
        $sensor = Sensor::firstOrCreate([
            'nombre' => 'TDS',
            "modelo" => "TDS con sonda sumergible",
            "unidad_medida" => "ppm",
        
        
        ]);
        $rango = Rango::firstOrCreate([
            'rango_min' => 0,
            'rango_max' => 2000,
           
        ]);

        $Valor = Valor::create([
            'id_sensor' => $sensor->id,
            "id_rango" => $rango->id,
            'value' => $valor,
            'unidad' => 'ppm',
        ]);

        $sensor->save();
        $Valor->save();
    }
}
