<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Tinaco;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ultrasonicoController extends Controller
{

    //NOTA: EL SENSOR DE ULTRASONICO 1
    public function obtenerturbidez(Request $request)
    {
        $tinacoId = $request->input('tinaco_id');
        $tinaco = Tinaco::find($tinacoId);

        $valores = DB::connection('mongodb')
            ->collection('Valor')
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$valores) {
            return response()->json(['mensaje' => 'Sensor ultrasonico no encontrado para el tinaco especificado'], 404);
        }

        $valores = $valores->where('tinaco_id', $tinaco->id);
        $valores = $valores->where('sensor_id', 1);
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
        ->join('sensor', 'sensor.sensor_id', '=', 'sensor.id')
        ->where('sensor.nombre', 'Ultrasonico') 
        ->first();

        if (!$Valor) {
            return response()->json(['mensaje' => 'Sensor ultrasonico no encontrado para el tinaco especificado'], 404);
        }
        $sensor = $Valor->sensor;

        $data = $this->adafruitService->getFeedData("ultrasonico");
        $this->guardarDatos($Valor,$tinaco,$data, $sensor, $usuario);
    
        $mensaje = $this->significadoDatos($data);
    
        return response()->json(['mensaje' => $mensaje]);
   
    }*/

    public function significadodatos($data)
    {

        $data = is_string($data) ? json_decode($data) : $data;

        $valor = $data['last_value'] ?? null;

        if (is_null($valor)) {


            return "No hay datos de altura para calcular";
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;

        if (is_null($valor)) {
            return "El valor de la altura no es numérico";
        }
        if ($valor >= 20) {

            return "Underflow: {$valor}";
        } else if ($valor <= 20) {
            return "overflow: {$valor}";
        }


        return "valor fuera de Sensor: {$valor}";
    }





    //switch ($data->data->value) 



    // case 0:
    //   return "Temperatura baja";
    //break;

    public function guardarDatos($Valor, $tinaco, $data, $sensor, $usuario)
    {
        $data = is_string($data) ? json_decode($data) : $data;

        $valor = $data['last_value'] ?? null;

        if (is_null($valor)) {


            return "Datos de tds no disponibles";
        }
        $valor = trim($valor);
        $valor = is_numeric($valor) ? (float) $valor : null;


        //por si no habia
        /* $sensor = Sensor::firstOrCreate([
                'nombre' => 'Ultrasonico',
                "modelo" => "JSN-SR04T-2.0",
                "unidad_medida" => "cm",
            
            
            ]); */
        /*   $Sensor = Sensor::firstOrCreate([
                'Sensor_min' => 20,
                'Sensor_max' => 600,
               
            ]); */

        $Valor = Valor::create([
            'value' => $valor,
            'id_sensor' => 1
        ]);
        // $sensor->save();
        $Valor->id_valor = $Valor->id;
        $Valor->save();

        $Valor->save();
    }
}
