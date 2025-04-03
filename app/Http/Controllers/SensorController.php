<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Validator;
use App\Models\Tinaco;
use App\Events\Sensores;
use Illuminate\Support\Facades\Log;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        Log::info('DB Config', [
            'uri' => env('DB_URI'),
            'db' => env('DB_NAME'),
            'collection' => env('DB_COLLECTION')
        ]);

        // Verificar si es un batch de datos
        if ($request->has('batch') && is_array($request->batch)) {
            return $this->processBatch($request);
        }

        // Procesamiento individual (mantenido para retrocompatibilidad)
        return $this->processSingle($request);
    }

    protected function processBatch(Request $request)
    {
        Log::info('Iniciando procesamiento de batch', ['payload_count' => count($request->batch)]);

        try {
            $payloads = $request->batch;
            $results = [];
            $errors = [];

            foreach ($payloads as $index => $payload) {
                try {
                    $validator = Validator::make($payload, [
                        'sensor_id' => 'required|integer',
                        'tinaco_id' => 'required|integer',
                        'valor' => 'required|numeric',
                    ]);

                    if ($validator->fails()) {
                        $errors[$index] = $validator->errors()->all();
                        Log::warning('Validación fallida', ['index' => $index, 'errors' => $validator->errors()]);
                        continue;
                    }

                    $result = $this->processPayload($payload);
                    $results[] = $result;
                } catch (\Exception $e) {
                    Log::error('Error procesando payload', ['index' => $index, 'error' => $e->getMessage()]);
                    $errors[$index] = $e->getMessage();
                }
            }

            Log::info('Resultados procesados', ['success' => count($results), 'errors' => count($errors)]);

            if (empty($results)) {
                Log::error('No hay resultados válidos para insertar');
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se pudo procesar ningún payload',
                    'errors' => $errors
                ], 400);
            }

            $db_uri = env('DB_URI') ?? 'mongodb://adminsillo:12341234@107.23.182.24:27017,18.212.189.87:27017,44.201.205.233:27017/?authSource=Monguillodb&replicaSet=rs0&retryWrites=true&w=majority&maxPoolSize=500';
            $database_name = env('DB_NAME') ?? 'Monguillodb';
            $collection_name = env('DB_COLLECTION') ?? 'Valor';

            try {
                $client = new MongoClient($db_uri);
                $collection = $client->$database_name->$collection_name;

                $insertResult = $collection->insertMany($results);
                Log::info('Insertados en MongoDB', ['inserted_count' => $insertResult->getInsertedCount()]);

                return response()->json([
                    'status' => !empty($errors) ? 'partial_success' : 'success',
                    'message' => !empty($errors) ? 'Algunos datos fueron insertados' : 'Todos los datos fueron insertados correctamente',
                    'inserted_count' => count($results),
                    'error_count' => count($errors),
                    'errors' => $errors,
                    'data' => $results
                ], !empty($errors) ? 207 : 200);
            } catch (\Exception $e) {
                Log::error('Error de MongoDB', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de base de datos',
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error general en processBatch', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function processSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'required|integer',
            'tinaco_id' => 'required|integer',
            'valor' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $this->processPayload($request->all());

        $db_uri = env('DB_URI');
        $client = new MongoClient($db_uri);
        $database_name = env('DB_NAME') ?? 'Monguillodb';
        $collection_name = env('DB_COLLECTION') ?? 'Valor';
        $collection = $client->$database_name->$collection_name;

        $collection->insertOne($data);

        // broadcast(new Sensores($data));

        return response()->json([
            'status' => 'success',
            'message' => 'Datos insertados correctamente',
            'data' => $data,
            'db_uri' => $db_uri,
            'database_name' => $database_name,
            'collection_name' => $collection_name
        ], 200);
    }

    /**
     * Procesa el payload, actualizando el nivel de agua si es necesario y enviando el broadcast.
     */
    protected function processPayload(array $payload)
    {
        $data = [
            'sensor_id'  => (string) $payload['sensor_id'],
            'tinaco_id'  => (string) $payload['tinaco_id'],
            'valor'      => (string) $payload['valor'],
            'created_at' => (string) ($payload['timestamp'] ?? date('Y-m-d H:i:s'))
        ];

        // Si es el sensor ultrasonico, actualizamos el tinaco
        if ($data['sensor_id'] == 1) {
            $this->updateTinacoNivel($data['tinaco_id'], $data['valor']);
        }

        // Broadcast de eventos (si aplica)
        try {
            broadcast(new Sensores($data));
        } catch (\Exception $e) {
            Log::error('Error broadcasting event', ['error' => $e->getMessage()]);
        }

        return $data;
    }

    /**
     * Actualiza el nivel del agua del tinaco basado en el valor del sensor ultrasonico.
     */
    protected function updateTinacoNivel($tinacoId, $valor)
    {
        $tinaco = Tinaco::find($tinacoId);
        if ($tinaco) {
            $tinacoHeight = 17; // Altura total del tinaco en cm
            $sensorValue = floatval($valor); // Lectura del sensor (en cm)
            $waterHeight = $tinacoHeight - $sensorValue;
            $waterHeight = max(0, min($waterHeight, $tinacoHeight));
            $percentage = ($waterHeight / $tinacoHeight) * 100;

            $tinaco->nivel_del_agua = round($percentage);
            $tinaco->save();
        }
    }
}
 