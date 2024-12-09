<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Rango;
use Illuminate\Support\Facades\Validator;

use App\Models\Tinaco;
use App\Models\SensorTinaco;
use Illuminate\Support\Facades\Auth;

class phController extends Controller
{
    protected $adafruitService;
        //falta poner bd

    public function __construct(AdafruitService $adafruitService)
    {
        $this->adafruitService = $adafruitService;
    }

   
    public function obtenerph(Request $request)
    {
        //Validamos
        $validator = Validator::make($request->all(), [
            'tinaco_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => $validator->errors()
            ], 400);
        }

        $usuario = Auth::user();
        $tinacoId = $request->input('tinaco_id');
       
        $tinaco = Tinaco::find($tinacoId);
        
        $sensorTinaco = SensorTinaco::where('tinaco_id', $tinaco->id)
        ->join('sensor', 'sensor_tinaco.sensor_id', '=', 'sensor.id')
        ->where('sensor.nombre', 'Ph') 
        ->first();

        if (!$sensorTinaco) {
            return response()->json(['mensaje' => 'Sensor de ph no encontrado para el tinaco especificado'], 404);
        }
        $sensor = $sensorTinaco->sensor;

        $data = $this->adafruitService->getFeedData("ph");
        $this->guardarDatos($data, $sensor, $usuario);

        $mensaje = $this->significadoDatos($data);

        return response()->json(['mensaje' => $mensaje]);



        


        
    }

        public function significadodatos($data)
        {
            
            $data = is_string($data) ? json_decode($data) : $data;
            
            $valor = $data['last_value'] ?? null;

            if (is_null($valor)) 
            {


                return "No hay datos de ph dispoibles para calcular";

                
            }
            $valor = trim($valor);
            $valor = is_numeric($valor) ? (float) $valor : null;

           // $ultimotemperatura = Valor::where('id_sensor', 6)->latest("id")->first();
             //    if ($ultimotemperatura) {
            //        $ultimoValor = (float) $ultimotemperatura->value;
            //     } else {
             //        return "no hay ningun ultimo registro de temperatura";
             //    }

           //  $factorescala= ((2.303 * 8.314 * ($valor + 273.15))/96485);
          //   $phfinal= (7 - (($valor - 2.5)/$factorescala));
            
            

         

            $factorescala = 0.3571;

            $phfinal = 7 - (($valor - 2.5) / $factorescala);
            

            if (is_null($phfinal)) {
                return "El valor de temperatura no es numérico";
            }
            if ($phfinal >= 6.5 || $phfinal <= 8.5) 
            {

                return "Seguro para tomar agua: {$phfinal}";
            } 
            else if ($phfinal >= 5.5 || $phfinal <= 7) 
            {
                return "Seguro para bañarse: {$phfinal}";
            } 
            else if ($phfinal >= 10 || $phfinal <= 4)
             {
                return "NO TENER CONTACTO CON ELLA PRECUACION: {$phfinal}";
             }

      
    
            return "ph fuera de rango: {$valor}°C";
        }
            




            //switch ($data->data->value) 
            
               

               // case 0:
                 //   return "Temperatura baja";
                    //break;
            
            public function guardarDatos($data, $sensor, $usuario)
            {
            $data = is_string($data) ? json_decode($data) : $data;
            
            $valor = $data['last_value'] ?? null;

            if (is_null($valor)) 
            {


                return "Datos de ph no disponibles";

                
            }
            $valor = trim($valor);
            $valor = is_numeric($valor) ? (float) $valor : null;
               $factorescala = 0.3571;

            $phfinal = 7 - (($valor - 2.5) / $factorescala);
            
            
            //por si no habia
           /*  $sensor = Sensor::firstOrCreate([
                'nombre' => 'Ph',
                "modelo" => "PH4502-C",
                "unidad_medida" => "ph",
            
            
            ]); */
            $rango = Rango::firstOrCreate([
                'rango_min' => 0,
                'rango_max' => 14,
               
            ]);

            $Valor = Valor::create([
                'id_sensor' => $sensor->id,
                "id_rango" => $rango->id,
                'value' => $phfinal,
                'unidad' => 'ph',
            ]);

            //$sensor->save();
            $Valor->save();
        }
}
