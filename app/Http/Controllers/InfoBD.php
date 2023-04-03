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
        ->max('salas.idsala');      //Integración en código para no utilizar campo autoincrement desde la base de datos
        if($idsala==null){
            $idsala=0;
        }

        $nuevaSala = DB::table('salas')
        ->insert([
            ['salas.idsala'=>$idsala+1,'salas.descripcion'=>$vdescripcion,
            'salas.capacidad'=>$vcapacidad]
        ]);
    }

    public function VerSalas(Request $request)
    {

        $ListaSalas = DB::table('salas')
        ->select('salas.idsala','salas.descripcion',
                'salas.capacidad')                   //Sentencia SQL adaptada a eloquent para mostrar la lista de todas las salas
        ->orderBy('salas.idsala','ASC')
        ->get();

        return json_encode($ListaSalas);        
    }

    public function ModificarSalas(Request $request)       //Api que modifica la información de una sala
    {
        $vidsala= $request->input('idsala');
        $vdescripcion= $request->input('descripcion');  //Datos recibidos desde el front
        $vcapacidad= $request->input('capacidad');

            $actualizar = DB::table('salas')
            ->where('salas.idsala','=',$vidsala)                      //Sentencia SQL adaptada a eloquent para actualizar los datos de una sala
            ->update(['salas.descripcion' => $vdescripcion,'salas.capacidad'=>$vcapacidad]);
    }

    public function VerSalaEspecifica(Request $request)
    {
        $vidsala= $request->input('idsala');
        $respuesta = [];

        $ListaSalas = DB::table('salas')
        ->select('salas.idsala','salas.descripcion',
                'salas.capacidad')                   //Sentencia SQL adaptada a eloquent para mostrar la lista de todas las salas
        ->where('salas.idsala','=',$vidsala)
        ->get();
        
        $respuesta[0]= $ListaSalas->first()->descripcion;
        $respuesta[1]= $ListaSalas->first()->capacidad;

        return json_encode($respuesta);        
    }

    public function EliminarSalas(Request $request)  //Api utilizada para eliminar salas
    {
        $vidsala= $request->input('idsala');  //id de la sala recibido desde el front

            $Eliminar = DB::table('salas')
            ->where('idsala','=',$vidsala)    //Sentencia SQL adaptada a eloquent para eliminar la sala seleccionada
            ->delete();
    }

    public function VerDisponibilidad(Request $request)
    {

        $vidsala= $request->input('idsala');  //id de la sala recibido desde el front
        $vifechareservacion= $request->input('fechareservacion');
        $vhorainicio= $request->input('horainicio');
        $vminutoinicio= $request->input('minutoinicio');
        $vhorafin= $request->input('horafin');
        $vminutofin= $request->input('minutofin');
        $respuesta = []; 

        $Disponibilidad = DB::table('reservaciones')
        ->select('reservaciones.idsala')            //Sentencia SQL adaptada a eloquent para mostrar la lista de todas las salas
        ->where('idsala','=',$vidsala)
        ->where('estado','=',1)
        ->where('diareservacion',"=",$vifechareservacion)
        ->where('horainicio',"=",$vhorainicio)
        ->where('minutoinicio',"<=",$vminutoinicio)
        ->where('horainicio',"<=",$vhorafin)
        ->where('minutofin','>=',$vminutofin)
        ->get();

        if($Disponibilidad->isEmpty()){
            $respuesta[0]="Disponible";
        }else{
            $respuesta[0]="Ocupada";
        }
        
        return json_encode($respuesta);        
    }

    public function GuardarReservacion(Request $request) //Actualizado y testado
    {
        $vidsala= $request->input('idsala');  //id de la sala recibido desde el front
        $vifechareservacion= $request->input('fechareservacion');
        $vhorainicio= $request->input('horainicio');
        $vminutoinicio= $request->input('minutoinicio');
        $vhorafin= $request->input('horafin');
        $vminutofin= $request->input('minutofin');
        $vnpersonas = $request->input('npersonas');
        $vcliente   = $request->input('cliente');


        $idreservacion = DB::table('reservaciones')
        ->max('reservaciones.idreservacion');      //Integración en código para no utilizar campo autoincrement desde la base de datos
        if($idreservacion==null){
            $idreservacion=0;
        }

        $nuevaReservacion = DB::table('reservaciones')
        ->insert([
            ['reservaciones.idreservacion'=>$idreservacion+1,'reservaciones.idsala'=>$vidsala,
            'reservaciones.diareservacion'=>$vifechareservacion,'reservaciones.horainicio'=>$vhorainicio,
            'reservaciones.minutoinicio'=>$vminutoinicio,'reservaciones.horafin'=>$vhorafin,
            'reservaciones.minutofin'=>$vminutofin,'reservaciones.npersonas'=>$vnpersonas,
            'reservaciones.cliente'=>$vcliente,'reservaciones.estado'=>1]
        ]);
    }

    public function VerSalasReservadas(Request $request)
    {

        $ListaSalas = DB::table('reservaciones')
        ->select('reservaciones.idsala','reservaciones.estado',
                'reservaciones.diareservacion','reservaciones.horainicio', 
                'reservaciones.minutoinicio', 'reservaciones.horafin', 'reservaciones.minutofin',
                'reservaciones.npersonas','reservaciones.cliente')                   //Sentencia SQL adaptada a eloquent para mostrar la lista de todas las salas con reservacion activa
        ->orderBy('reservaciones.idsala','ASC')
        ->where('reservaciones.estado',"=",1)
        ->get();

        return json_encode($ListaSalas);        
    }

    public function LiberarSala(Request $request)       //Api que modifica la información de una sala
    {
        $vidreservacion= $request->input('idreservacion');

            $actualizar = DB::table('reservaciones')
            ->where('reservaciones.idreservacion','=',$vidreservacion)                      //Sentencia SQL adaptada a eloquent para actualizar los datos de una sala
            ->update(['reservaciones.estado' => 0]);    //El 0 en el campo estado indica que se ha concluido la reservación
    }

    public function VerSalasReservadasUsuario(Request $request)
    {
        $vusuario= $request->input('usuario');

        $ListaSalas = DB::table('reservaciones')
        ->select('reservaciones.idsala','reservaciones.estado',
                'reservaciones.diareservacion','reservaciones.horainicio', 
                'reservaciones.minutoinicio', 'reservaciones.horafin', 'reservaciones.minutofin',
                'reservaciones.npersonas','reservaciones.cliente','reservaciones.idreservacion')                   //Sentencia SQL adaptada a eloquent para mostrar la lista de todas las salas con reservacion activa
        ->orderBy('reservaciones.idsala','ASC')
        ->where('reservaciones.estado',"=",1)
        ->where('reservaciones.cliente','=',$vusuario)
        ->get();

        return json_encode($ListaSalas);        
    }

}
