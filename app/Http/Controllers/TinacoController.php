<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tinaco;
use App\Models\Valor;
use Illuminate\Support\Facades\Validator;

class TinacoController extends Controller
{

    public function agregarTinaco(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        // Validar si el tinaco ya existe
        $tinacos = Tinaco::where('name', $request->name)->where('id_usuario', auth()->user()->id)->first();
        if ($tinacos) {
            return response()->json(['message' => 'El tinaco ya existe con ese nombre.'], 400);
        }
        $id_usuario = auth()->user()->id;

        //se crea
        $tinaco = new Tinaco();
        $tinaco->name = $request->name;
        $tinaco->id_usuario = $id_usuario;
        $tinaco->nivel_del_agua = 0;
        $tinaco->save();

        //registros valor
        //agremaso los 5 sensores por defecto
        $sensor_ids = [1, 2, 3, 4, 5];
        foreach ($sensor_ids as $sensor_id) {
            $tinaco_sensor = new Valor();
            $tinaco_sensor->sensor_id = $sensor_id;
            $tinaco_sensor->tinaco_id = $tinaco->id;
            $tinaco_sensor->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Tinaco creado correctamente.'
        ], 201);
    }

    public function listartinacos()
    {
        $id_usuario = auth()->user()->id;
        $tinacos = Tinaco::where('id_usuario', $id_usuario)->get();

        return response()->json($tinacos, 200);
    }
    public function listartinacos2()
    {
        $id_usuario = auth()->user()->id;
        //USAMOS PAGINACIÓN
        $tinacos = Tinaco::where('id_usuario', $id_usuario)->paginate(5);
        

        return response()->json($tinacos, 200);
    }

    public function eliminartinaco($id)
    {
        $tinaco = Tinaco::find($id);
        //veremos si el tinaco existe
        if (!$tinaco) {
            return response()->json(['message' => 'El tinaco no existe.'], 404);
        }
        $tinaco->delete();
        return response()->json(['message' => 'Tinaco eliminado correctamente.'], 200);
    }

    public function actualizartinaco(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        $tinaco = Tinaco::find($id);
        //veremos si el tinaco existe
        if (!$tinaco) {
            return response()->json(['message' => 'El tinaco no existe.'], 404);
        }
        $tinacos = Tinaco::where('name', $request->name)->where('id_usuario', auth()->user()->id)->first();
        if ($tinacos) {
            return response()->json(['message' => 'El tinaco ya existe con ese nombre.'], 400);
        }
        $tinaco->name = $request->name;
        $tinaco->save();
        return response()->json(['message' => 'Tinaco actualizado correctamente.'], 200);
    }
    
    public function gettinaco($id)
    {
        $tinaco = Tinaco::find($id);
        //veremos si el tinaco existe
        if (!$tinaco) {
            return response()->json(['message' => 'El tinaco no existe.'], 404);
        }
        return response()->json(['tinaco' => $tinaco], 200);
    }
}
