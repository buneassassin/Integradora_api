<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tinaco;
use Illuminate\Support\Facades\Validator;

class TinacoController extends Controller
{

    public function agregartinaco(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        //validar si el tinaco ya existe
        $tinaco = Tinaco::where('name', $request->name)->first();
        if ($tinaco) {
            return response()->json(['message' => 'El tinaco ya existe con ese nombre.'], 400);
        }
        //sacamos el id del usuario del autenticador
        $id_usuario = auth()->user()->id;

        $tinaco = new Tinaco();
        $tinaco->name = $request->name;
        $tinaco->id_usuario = $id_usuario;
        $tinaco->save();
        return response()->json(['message' => 'Tinaco creado correctamente.'], 201);
    }
    public function listartinacos(){
        $id_usuario = auth()->user()->id;
        $tinacos = Tinaco::where('id_usuario', $id_usuario)->get();
        return response()->json($tinacos, 200);
    }
    public function eliminartinaco($id){
        $tinaco = Tinaco::find($id);
        $tinaco->delete();
        return response()->json(['message' => 'Tinaco eliminado correctamente.'], 200);
    }
    public function actualizartinaco(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }
        $tinaco = Tinaco::find($id);
        $tinaco->name = $request->name;
        $tinaco->save();
        return response()->json(['message' => 'Tinaco actualizado correctamente.'], 200);
    }
    public function gettinaco($id){
        $tinaco = Tinaco::find($id);
        return response()->json($tinaco, 200);
    }
    
}
