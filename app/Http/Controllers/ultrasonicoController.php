<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Rango;
class ultrasonicoController extends Controller
{
    protected $adafruitService;

    public function __construct(AdafruitService $adafruitService)
    {
        $this->adafruitService = $adafruitService;
    }
    
    
    public function obtenerturbidez()
    {
        $data = $this->adafruitService->getFeedData("ultrasonico");
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
    
    
                return "No hay datos de altura para calcular";
    
                
            }
            $valor = trim($valor);
            $valor = is_numeric($valor) ? (float) $valor : null;
        
            if (is_null($valor)) {
                return "El valor de la altura no es numérico";
            }
            if ($valor >= 20) 
            {
    
                return "Underflow: {$valor}";
            } 
       
             else if ($valor <= 20)
             {
                return "overflow: {$valor}";
             }
      
    
            return "valor fuera de rango: {$valor}";
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
                'nombre' => 'Ultrasonico',
                "modelo" => "JSN-SR04T-2.0",
                "unidad_medida" => "cm",
            
            
            ]);
            $rango = Rango::firstOrCreate([
                'rango_min' => 20,
                'rango_max' => 600,
               
            ]);
    
            $Valor = Valor::create([
                'id_sensor' => $sensor->id,
                "id_rango" => $rango->id,
                'value' => $valor,
                'unidad' => 'cm',
            ]);
    
            $sensor->save();
            $Valor->save();
        }}
