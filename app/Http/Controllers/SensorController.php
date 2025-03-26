<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Validator;
use App\Models\Tinaco;
use App\Events\Sensores;

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
        // si el sensor_id es 1 es ultrasonico actualizamos la base de datos de el nivel de agua del tinaco
        $sensor_id = $request->input('sensor_id');

        if ($sensor_id == 1) {
            $tinaco_id = $request->input('tinaco_id');
            $nivel = $request->input('valor');
            $tinaco = Tinaco::find($tinaco_id);
            $tinaco->nivel_del_agua = $nivel;
            $tinaco->save();
        }

        $timestamp = $request->input('timestamp')??date('Y-m-d H:i:s');

        // Obtener la URI de MongoDB desde el archivo .env
        $db_uri = env('DB_URI')?? 'mongodb+srv://myAtlasDBUser:absdefg@myatlasclusteredu.hhf3j.mongodb.net/retryWrites=true&w=majority&appName=myAtlasClusterEDU';

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
        broadcast(new Sensores($data));

        return response()->json(['status' => 'success', 'message' => 'Datos insertados correctamente', 'data' => $data,'db_uri' => $db_uri,'database_name' => $database_name,'collection_name' => $collection_name,'collection' => $collection], 200);
    }
}