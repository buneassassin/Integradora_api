<?php

namespace App\Http\Controllers;

use App\Models\Valor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Mongodb\Eloquent\Model;

class ReporteController extends Controller
{
    public function obtenerDatos()
    {
        try {
                $resultados=[];
                $valor = DB::connection('mongodb')
                ->collection('Valor')
                ->get();

                $sensores = DB::connection('mysql')
                ->table('sensor')
                ->select(
                    'sensor.nombre',
                    'sensor.id',

                )
                ->get();
                $tinacos = DB::connection('mysql')
                ->table('tinaco')
                ->select(
                    'tinaco.name',
                    'tinaco.id',
                    'tinaco.id_usuario'

                )
                ->get();

                foreach ($valor as $valores) 
                {
                    // QUIEN FUE EL QUE INTRODUJO REGISTROS DE VALOR SIN VALOR ME ESTABA DANDO ERROR POR ESO AAAAAAAAAAAA
                    $sensor = $sensores->firstWhere('id', $valores['sensor_id']);
                    // Busca el tinaco relacionado
                    $tinaco = $tinacos->firstWhere('id', $valores['tinaco_id']);                  
                    

                  if ($sensor && $tinaco) 
                  {
                    $resultados[] = [
                        'valor' => $valores['valor'], 
                        'sensor_nombre' => $sensor ->nombre,
                        'tinaco_nombre' => $tinaco ->name,
                        'id_usuario' => $tinaco ->id_usuario,
                  
                    ];
                    
                }
                }
                /*
                ->table('sensor_tinaco')
                    ->join('sensor', 'sensor_tinaco.sensor_id', '=', 'sensor.id')
                    ->select(
                        'sensor.nombre',
                        'sensor.id as id_sensor',
                        DB::raw('AVG(sensor_tinaco.valor) as promedio_valor'),
                        DB::raw('COUNT(sensor_tinaco.valor) as cantidad_lecturas')
                    )
                    ->groupBy('sensor.id', 'sensor.nombre')
                    ->orderByDesc('cantidad_lecturas')
                    ->get();
                */
                
              
            // Retornamos los datos
            return response()->json([
                'status' => 'success',
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
      // Consulta para obtener datos de los sensores asociados a tinacos
     

    public function obtenerDatosPorFecha()
    {
        try {
            // Consulta para obtener datos agrupados por fecha
            $datos = DB::connection('mongodb')
            ->collection('Valor')
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('AVG(valor) as promedio_valor'),
                DB::raw('valor as valor'),
            )
            ->get();
        
            // Formato de respuesta
            return response()->json([
                'status' => 'success',
                'data' => $datos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function obtenerHistorialPorSensor(Request $request)
    {
        try {
            // Validamos que el nombre del sensor sea enviado en la solicitud
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }
            $id_sensor=0;
            // Obtenemos el nombre del sensor desde el body de la solicitud
            $nombreSensor = $request->input('nombre');
                        //vereficamos si el nombre es Ultrasonico

            if ($nombreSensor == 'Ultrasonico') {
               $id_sensor=1;
            }
            if ($nombreSensor == 'Temperatura') {
                $id_sensor=2;
             }
             if ($nombreSensor == 'PH' || $nombreSensor == 'ph' || $nombreSensor == 'Ph') {
                $id_sensor=3;
             }
             if ($nombreSensor == 'Turbidez') {
                $id_sensor=4;
             }
             if ($nombreSensor == 'TDS') {
                $id_sensor=5;
             }
            
            // Consulta para obtener el historial del sensor filtrado por nombre
            $datos = DB::table('valor')
                ->select(
                    'valor.*'
                )
                ->where('valor.id_sensor', '=', $id_sensor)
                ->orderBy('valor.created_at', 'desc')
                ->get();
            
    
            // Verificamos si se encontraron datos
            if ($datos->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron datos para el sensor especificado.'
                ], 404);
            }
            //juntamos los datos con el nombre del sensor
            foreach ($datos as $dato) {
                $dato->nombre = $nombreSensor;
            }
    
            // Retornamos los datos completos del historial del sensor
            return response()->json([
                'status' => 'success',
                'data' => $datos
            ], 200);
    
        } catch (\Exception $e) {
            // En caso de error, devolvemos el mensaje del error
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}
