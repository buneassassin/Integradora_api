<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ReporteController extends Controller
{
    public function obtenerDatos()
    {
        try {
            // Consulta para obtener datos del sensor con nombres
            $datos = DB::table('valor')
                ->join('sensor', 'valor.id_sensor', '=', 'sensor.id') // Unir tablas
                ->select(
                    'sensor.nombre',
                    'valor.id_sensor',
                    DB::raw('AVG(valor.value) as promedio_valor'),
                    DB::raw('COUNT(valor.value) as cantidad_lecturas')
                )
                ->groupBy('valor.id_sensor', 'sensor.nombre') // Agrupar por id_sensor y nombre_sensor
                ->orderByDesc('cantidad_lecturas') // Ordenar por cantidad de lecturas
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
    public function obtenerDatosPorFecha()
    {
        try {
            // Consulta para obtener datos agrupados por fecha
            $datos = DB::table('valor')
                ->select(
                    DB::raw('DATE(created_at) as fecha'),
                    DB::raw('COUNT(*) as cantidad_lecturas')
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'), 'ASC')
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
    public function obtenerDatosPorSensor(Request $request)
    {
        try {

            // Validamos que el nombre del sensor sea enviado
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // Obtenemos el nombre del sensor desde el body
            $nombreSensor = $request->input('nombre');

            // Consulta para obtener los datos completos del sensor
            $datos = DB::table('valor') // Asumiendo que 'valor' es la tabla donde están los valores
                ->join('sensor', 'valor.id_sensor', '=', 'sensor.id')
                ->select('sensor.nombre', 'sensor.id as id_sensor', 'valor.value', 'valor.created_at')
                ->where('sensor.nombre', '=', $nombreSensor)
                ->orderBy('valor.created_at', 'asc') // Para obtener los valores históricos en orden cronológico
                ->get();

            // Verificamos si se encontraron datos
            if ($datos->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron datos para el sensor especificado.'
                ], 404);
            }

            // Retornamos los datos completos del sensor
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
