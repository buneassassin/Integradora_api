<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Validator;

class SensorController extends Controller
{
    public function store(Request $request)
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
        $timestamp = $request->input('timestamp')??date('Y-m-d H:i:s');

        // Obtener la URI de MongoDB desde el archivo .env
        $db_uri = env('DB_URI');

        // Conectar a MongoDB usando la URI
        $client = new MongoClient($db_uri);

        // Seleccionar la base de datos y la colección usando las variables de entorno
        $database_name = env('DB_NAME') ?? 'Monguillodb'; // Nombre de la base de datos
        $collection_name = env('DB_COLLECTION') ?? 'Valor'; // Nombre de la colección
        $collection = $client->$database_name->$collection_name;

        // Transformar el campo "timestamp" en "created_at"
        $data = $request->all();
        $data['created_at'] =  $timestamp ; // Asignar el valor de timestamp a created_at
        unset( $timestamp ); // Eliminar el campo timestamp del arreglo

        // Insertar los datos en MongoDB
        $collection->insertOne($data);

        return response()->json(['status' => 'success'], 200);
    }
}