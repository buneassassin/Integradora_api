<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Validator;
use App\Models\Tinaco;
use App\Events\Sensores;
use Illuminate\Support\Facades\Log;

//QUE NO SE ME OLVIDE DESCOMENTAR LO DE BROADCAST

class SensorController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('DB Config', [
            'uri' => env('DB_URI'),
            'db' => env('DB_NAME'),
            'collection' => env('DB_COLLECTION')
        ]);

        // Verificar si es un batch de datos
        if ($request->has('batch') && is_array($request->batch)) {
            return $this->processBatch($request);
            // 1- detectamos que es un conjunto de muchos payloads y lo mandamos a procesar
        }

        // Procesamiento individual (mantenido para retrocompatibilidad but ya no se usará tbh)
        return $this->processSingle($request);
    }

    protected function processBatch(Request $request)
    {
        \Log::info('Iniciando procesamiento de batch', ['payload_count' => count($request->batch)]);
    
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
                    // 2- recorremos el array para procesar cada entrada

                    if ($validator->fails()) {
                        $errors[$index] = $validator->errors()->all();
                        \Log::warning('Validación fallida', ['index' => $index, 'errors' => $validator->errors()]);
                        continue;
                    }
                    
                    // 3- ya validado, manamos a procesar cada posible registro (payload)
                    $result = $this->processPayload($payload);
                    $results[] = $result;
                    
                } catch (\Exception $e) {
                    \Log::error('Error procesando payload', ['index' => $index, 'error' => $e->getMessage()]);
                    $errors[$index] = $e->getMessage();
                }
            }
    
            \Log::info('Resultados procesados', ['success' => count($results), 'errors' => count($errors)]);
            //dd($results); // Conjunto de datos a insertar

            if (empty($results)) {
                \Log::error('No hay resultados válidos para insertar');
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se pudo procesar ningún payload',
                    'errors' => $errors
                ], 400);
            }

            // Paso que creo que falta: Conectar a Mongo de una manera más fuerte
            $db_uri = env('DB_URI')?? 'mongodb+srv://myAtlasDBUser:absdefg@myatlasclusteredu.hhf3j.mongodb.net/retryWrites=true&w=majority&appName=myAtlasClusterEDU';
            $database_name = env('DB_NAME') ?? 'Monguillodb'; // Nombre de la base de datos
            $collection_name = env('DB_COLLECTION') ?? 'Valor'; // Nombre de la colección

            // Conexión a MongoDB con manejo de errores
            try {
                // Conectar a MongoDB usando la URI
                $client = new MongoClient($db_uri);
                // Seleccionar la base de datos y la colección usando las variables de entorno
                $collection = $client->$database_name->$collection_name;
                
                $insertResult = $collection->insertMany($results);
                \Log::info('Insertados en MongoDB', ['inserted_count' => $insertResult->getInsertedCount()]);
        
                return response()->json([
                    'status' => !empty($errors) ? 'partial_success' : 'success',
                    'message' => !empty($errors) ? 'Algunos datos fueron insertados' : 'Todos los datos fueron insertados correctamente',
                    'inserted_count' => count($results),
                    'error_count' => count($errors),
                    'errors' => $errors,
                    'data' => $results
                ], !empty($errors) ? 207 : 200);
    
            } catch (\Exception $e) {
                \Log::error('Error de MongoDB', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de base de datos',
                    'error' => $e->getMessage()
                ], 500);
            }
    
        } catch (\Exception $e) {
            \Log::error('Error general en processBatch', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function processSingle(Request $request)
    {
        // Validar los datos recibidos
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

        // ----------------------------------------------
        // si el sensor_id es 1 es ultrasonico actualizamos la base de datos de el nivel de agua del tinaco
        $sensor_id = $request->input('sensor_id');
        if ($sensor_id == 1) {
            $tinaco_id = $request->input('tinaco_id');
            $nivel = $request->input('valor');
            $tinaco = Tinaco::find($tinaco_id);
            //dd($tinaco);
            $tinaco->nivel_del_agua = $nivel;
            $tinaco->save();
        }
        // termina bloque donde haces actualización visual del nivel de agua
        // ----------------------------------------------

        // Insertar en MongoDB
        $db_uri = env('DB_URI') ?? 'mongodb+srv://myAtlasDBUser:absdefg@myatlasclusteredu.hhf3j.mongodb.net/retryWrites=true&w=majority&appName=myAtlasClusterEDU';
        $client = new MongoClient($db_uri);
        $database_name = env('DB_NAME') ?? 'Monguillodb';
        $collection_name = env('DB_COLLECTION') ?? 'Valor';
        $collection = $client->$database_name->$collection_name;

        $collection->insertOne($data);

        //broadcast(new Sensores($data));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Datos insertados correctamente',
            'data' => $data,
            'db_uri' => $db_uri,
            'database_name' => $database_name,
            'collection_name' => $collection_name
        ], 200);
    }

    protected function processPayload(array $payload)
    {        
        // 4- Preparar datos para MongoDB
        $data = [
            'sensor_id' => $payload['sensor_id'],
            'tinaco_id' => $payload['tinaco_id'],
            'valor' => $payload['valor'],
            'created_at' => $payload['timestamp'] ?? date('Y-m-d H:i:s')
        ];

        // si el sensor_id es 1 es ultrasonico actualizamos la base de datos de el nivel de agua del tinaco
        if ($data['sensor_id'] == 1) {
            $tinaco_id = $data['tinaco_id'];
            //dd($tinaco_id);
            $nivel = $data['valor'];
            //dd($nivel);
            $tinaco = Tinaco::find($tinaco_id);
            //dd($tinaco);
            $tinaco->nivel_del_agua = $nivel;
            $tinaco->save();
        }

        // Broadcast de eventos
        /*
        try {
            broadcast(new Sensores($data));
        } catch (\Exception $e) {
            \Log::error('Error broadcasting event', ['error' => $e->getMessage()]);
        }
        */

        return $data;
    }
}