<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Rango;
use App\Models\Tinaco;
use Illuminate\Support\Facades\Log;



use App\Models\SensorTinaco;
use Illuminate\Support\Facades\Auth;
class TemperaturaController extends Controller
{
    protected $adafruitService;
        //falta poner bd

    public function __construct(AdafruitService $adafruitService)
    {
        $this->adafruitService = $adafruitService;
    }

   
    public function obtenertemp(Request $request)
    {
        $usuario = Auth::user();
        $tinacoId = $request->input('tinaco_id');
        $tinaco = Tinaco::find($tinacoId);

        $sensorTinaco = SensorTinaco::where('tinaco_id', $tinaco->id)
        ->join('sensor', 'sensor_tinaco.sensor_id', '=', 'sensor.id')
        ->where('sensor.nombre', 'Temperatura') 
        ->first();

        if (!$sensorTinaco) {
            return response()->json(['mensaje' => 'Sensor de temperatura no encontrado para el tinaco especificado'], 404);
        }
        $sensor = $sensorTinaco->sensor;
        $data = $this->adafruitService->getFeedData("temperatura");

        $mensaje = $this->significadoDatos($data);

        $guardarDatos = $this->guardarDatos($sensorTinaco, $tinaco,$data, $sensor, $usuario);

        return response()->json(['mensaje' => $mensaje]);



        


        
    }

        public function significadodatos($data)
        {
            
            $data = is_string($data) ? json_decode($data) : $data;
            
            $valor = $data['last_value'] ?? null;

            if (is_null($valor)) 
            {


                return "Datos de temperatura no disponibles";

                
            }
            $valor = trim($valor);
            $valor = is_numeric($valor) ? (float) $valor : null;
            if (is_null($valor)) {
                return "El valor de temperatura no es numérico";
            }
            if ($valor >= 30 || $valor <= 40) 
            {

                return "Temperatura ideal para bañarse: {$valor}°C";
            } 
            else if ($valor > 40 || $valor <= 50) 
            {
                return "Agua caliente, precaución, quemaduras: {$valor}°C";
            } 
            else if ($valor > 50)
             {
                return "Demasiado caliente, no usar: {$valor}°C";

            } 
            else if ($valor >= 15 || $valor < 20) 
            {
                return "Agua fría: {$valor}°C";
            }
            else if ($valor < 15)
             {
                return "Agua MUY fría: {$valor}°C";
            }
      
    
            return "Temperatura fuera de rango: {$valor}°C";
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


                return "Datos de temperatura no disponibles";

                
            }
            $valor = trim($valor);
            $valor = is_numeric($valor) ? (float) $valor : null;

            //por si no habia
          /*   $sensor = Sensor::firstOrCreate([
                'nombre' => 'Temperatura',
                "modelo" => "MAX6675",
                "unidad_medida" => "°C",
            
            
            ]); */
            /* $rango = Rango::firstOrCreate([
                'rango_min' => -200,
                'rango_max' => 700,
               
            ]); */

            $Valor = Valor::create([
                'value' => $valor,
                'id_sensor'=> 2

            ]);

            $sensorTinaco->id_valor = $Valor->id;

            $Valor->save();

            $sensorTinaco->save();
            
            

           // $sensor->save();
        }
}
