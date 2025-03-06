<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Tinaco;
use Illuminate\Support\Facades\Log;
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
        $tinacoId = $request->input('tinaco_id');
        $tinaco = Tinaco::find($tinacoId);

        $valores = Valor::where('tinaco_id', $tinaco->id)
        ->where('sensor_id', 2) // Filtramos donde el sensor_id sea 2
        ->orderBy('created_at', 'desc') // Ordenamos del más nuevo al más viejo
        ->first(); // Devolvemos solo el primero

        dd($valores);
        return $valores;
    }

    /*
----------------------------------------------------------------------------------------------resplado función trecker

  public function obtenertemp(Request $request)
    {
        $usuario = Auth::user();
        $tinacoId = $request->input('tinaco_id');
        $tinaco = Tinaco::find($tinacoId);

        $Valor = Valor::where('tinaco_id', $tinaco->id)
        ->join('sensor', 'valor.sensor_id', '=', 'sensor.id')
        ->where('sensor.nombre', 'Temperatura') 
        ->first();

        if (!$Valor) {
            return response()->json(['mensaje' => 'Sensor de temperatura no encontrado para el tinaco especificado'], 404);
        }
        $sensor = $Valor->sensor;
        $data = $this->adafruitService->getFeedData("temperatura");

        $mensaje = $this->significadoDatos($data);

        $guardarDatos = $this->guardarDatos($Valor, $tinaco,$data, $sensor, $usuario);

        return response()->json(['mensaje' => $mensaje]);
    }

--------------------------------------------------------------------------------------------
-respaldo lookup que igual no me salió

 public function obtenertemp(Request $request)
    {
        $usuario = Auth::user();

        $tinaco_id = $request->input('tinaco_id');
        $tinaco = Tinaco::find($tinaco_id);
        if (!$tinaco) {
            return response()->json(['mensaje' => 'Tinaco no encontrado'], 404);
        }
    
        $resultado = Valor::raw(function ($collection) use ($tinaco_id) {
            return $collection->aggregate([
                [
                    '$match' => ['tinaco_id' => $tinaco_id]
                ],
                [
                    '$lookup' => [
                        'from' => 'Sensor',
                        'localField' => 'sensor_id',
                        'foreignField' => 'id',
                        'as' => 'sensor'
                    ]
                ],
                [
                    '$unwind' => '$sensor'
                ],
                [
                    '$match' => ['sensor.nombre' => 'Temperatura']
                ],
                [
                    '$sort' => ['created_at' => -1]
                ],
                [
                    '$limit' => 1
                ]
            ]);
        });
        dd($resultado);
    
        // Verifica si se obtuvo un resultado
        if (!$resultado->isEmpty()) {
            $Valor = $resultado->first();
            $sensor = $Valor->sensor;
        } else {
            return response()->json(['mensaje' => 'Valor o sensor no encontrado para el tinaco especificado'], 404);
        }
    
        /*
        $data = $this->adafruitService->getFeedData("temperatura");
        $mensaje = $this->significadoDatos($data);
        $guardarDatos = $this->guardarDatos($Valor, $tinaco, $data, $sensor, $usuario);
        */
    
       // return response()->json(['mensaje' => $mensaje]);
   // }

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
      
    
            return "Temperatura fuera de Sensor: {$valor}°C";
        }
            




            //switch ($data->data->value) 
            
               

               // case 0:
                 //   return "Temperatura baja";
                    //break;
            
            public function guardarDatos($Valor,$tinaco,$data, $sensor, $usuario)
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
            /* $Sensor = Sensor::firstOrCreate([
                'Sensor_min' => -200,
                'Sensor_max' => 700,
               
            ]); */

            $Valor = Valor::create([
                'value' => $valor,
                'id_sensor'=> 2

            ]);

            $Valor->id_valor = $Valor->id;

            $Valor->save();

            $Valor->save();
            
            

           // $sensor->save();
        }
}
