<?php

namespace App\Http\Controllers;

use App\Models\login;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InfoBD extends Controller
{
    public function login(Request $request)
    {
        $respuesta = []; //Vector que almacena el rol del usuario, la ruta de acceso al front y el nombre de usuario
        $UserName= $request->input('name');
        $UserPassword= $request->input('password');

        $login = DB::table('Usuarios')
        ->select('Usuarios.rol','Usuarios.nombre','usuarios.vpassword')
        ->where('Usuarios.correo', '=', $UserName) 
        ->where('Usuarios.vpassword', '=', $UserPassword)
        ->get();

            if(!$login->isEmpty()){  //Se ejecuta en caso de que la contraseña y usuario coincidan en la base de datos
                if(($login->first()->vpassword==$UserPassword) && $login!=null){
                    $respuesta[0]= $login->first()->rol;
                    $respuesta[1]="home";
                    $respuesta[2]= $login->first()->nombre;  //Llenado de vector respuesta

                    return response()->json($respuesta); //Se envia la información al frontend

                }
            }else{ //En caso de que no coincida los datos
                    $respuesta[0]= "no encontrado";
                    $respuesta[1]="login";
                    $respuesta[2]= "no encontrado"; //Llenado de vector respuesta

                    return response()->json($respuesta);
            } 
            
        
    }

    public function GuardarNuevaSala(Request $request) //Actualizado y testado
    {
        $vdescripcion = $request->input('descripcion');
        $vcapacidad = $request->input('capacidad');


        $idsala = DB::table('salas')
        ->max('salas.idsala');
        if($idsala==null){
            $idsala=0;
        }

        $nuevaSala = DB::table('salas')
        ->insert([
            ['salas.idsala'=>$idsala+1,'salas.descripcion'=>$vdescripcion,
            'salas.capacidad'=>$vcapacidad,'salas.estado'=> 0]
        ]);

    }

}
