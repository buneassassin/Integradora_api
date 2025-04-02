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

        // Convertir todos los valores recibidos a string
        $sensor_id = (string) $request->input('sensor_id');
        $tinaco_id = (string) $request->input('tinaco_id');
        $nivel = (string) $request->input('valor');
        $timestamp = (string) ($request->input('timestamp') ?? date('Y-m-d H:i:s'));

        // Si el sensor_id es 1, actualizar el nivel del tinaco
        if ($sensor_id === '1') {
            $tinaco = Tinaco::find($tinaco_id);
            if ($tinaco) {
                $tinaco->nivel_del_agua = $nivel;
                $tinaco->save();
            }
        }

        // Obtener la URI de MongoDB desde el archivo .env
        $db_uri = env('DB_URI') ?? 'mongodb+srv://myAtlasDBUser:absdefg@myatlasclusteredu.hhf3j.mongodb.net/retryWrites=true&w=majority&appName=myAtlasClusterEDU';

        // Conectar a MongoDB usando la URI
        $client = new MongoClient($db_uri);

        // Seleccionar la base de datos y la colección
        $database_name = env('DB_NAME') ?? 'Monguillodb';
        $collection_name = env('DB_COLLECTION') ?? 'Valor';
        $collection = $client->$database_name->$collection_name;

        // Obtener todos los datos y convertirlos a string
        $data = array_map('strval', $request->all());
        $data['created_at'] = $timestamp; // Asignar el valor de timestamp a created_at

        // Insertar los datos en MongoDB
        $collection->insertOne($data);
        broadcast(new Sensores($data));

        return response()->json([
            'status' => 'success',
            'message' => 'Datos insertados correctamente',
            'data' => $data,
            'db_uri' => $db_uri,
            'database_name' => $database_name,
            'collection_name' => $collection_name
        ], 200);
    }
}
