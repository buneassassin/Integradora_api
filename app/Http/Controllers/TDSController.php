<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Sensor;
use App\Models\Tinaco;
use App\Models\Valor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class TDSController extends Controller
{

        //NOTA: EL SENSOR DE TDS 5
        public function obtenerturbidez(Request $request)
        {
            $tinacoId = $request->input('tinaco_id');
            $tinaco = Tinaco::find($tinacoId);
    
            $valores = DB::connection('mongodb')
                ->collection('Valor')
                ->get();
    
            if (!$valores) {
                return response()->json(['mensaje' => 'Sensor de TDS no encontrado para el tinaco especificado'], 404);
            }    
            $valores = $valores->where('tinaco_id', $tinaco->id);
            $valores = $valores->where('sensor_id', 5);
            $valores = $valores->take(1);

            return $valores;
        }

    /*
    protected $adafruitService;

public function __construct(AdafruitService $adafruitService)
{
    $this->adafruitService = $adafruitService;
}


public function obtenerturbidez(Request $request)
{
    $usuario = Auth::user();
    $tinacoId = $request->input('tinaco_id');
    $tinaco = Tinaco::find($tinacoId);

    $Valor = Valor::where('tinaco_id', $tinaco->id)
    ->join('sensor', 'valor.sensor_id', '=', 'sensor.id')
    ->where('sensor.nombre', 'TDS') 
    ->first();

    if (!$Valor) {
        return response()->json(['mensaje' => 'Sensor de TDS no encontrado para el tinaco especificado'], 404);
    }
    $sensor = $Valor->sensor;

    $data = $this->adafruitService->getFeedData("tds");
    $this->guardarDatos($Valor, $tinaco,$data, $sensor, $usuario);

    $mensaje = $this->significadoDatos($data);

    return response()->json(['mensaje' => $mensaje]);    
}*/

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
  

        return "turbidez fuera de Sensor: {$valor}";
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


            return "Datos de tds no disponibles";

            
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;
        
        
        //por si no habia
       /*  $sensor = Sensor::firstOrCreate([
            'nombre' => 'TDS',
            "modelo" => "TDS con sonda sumergible",
            "unidad_medida" => "ppm",
        
        
        ]); */
       /*  $Sensor = Sensor::firstOrCreate([
            'Sensor_min' => 0,
            'Sensor_max' => 2000,
           
        ]); */

        $Valor = Valor::create([
            'value' => $valor,
            'id_sensor'=> 5

        ]);
       // $sensor->save();
       $Valor->id_valor = $Valor->id;
       
       $Valor->save();

       $Valor->save();
    }
}
