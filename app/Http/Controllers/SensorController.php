<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'sensor_id' => 'required|integer',
            'tinaco_id' => 'required|integer',
            'valor' => 'required|numeric',
            'timestamp' => 'required|date'
        ]);

        // Obtener la URI de MongoDB desde el archivo .env
        $db_uri = env('DB_URI');

        // Conectar a MongoDB usando la URI
        $client = new MongoClient($db_uri);

        // Seleccionar la base de datos y la colección
        $database_name = 'u427674310_Integradora'; // Nombre de la base de datos
        $collection_name = 'Valor';    // Nombre de la colección
        $collection = $client->$database_name->$collection_name;

        // Transformar el campo "timestamp" en "created_at"
        $data = $request->all();
        $data['created_at'] = $data['timestamp']; // Asignar el valor de timestamp a created_at
        unset($data['timestamp']); // Eliminar el campo timestamp del arreglo

        // Insertar los datos en MongoDB
        $collection->insertOne($data);

        return response()->json(['status' => 'success'], 200);
    }
}