<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModelo extends Model
{
     protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre_usuario',
        'correo_electronico',
        'contrasena_hash',
        'id_rol',
        'estado',
        'imagen',
        'token_recuperacion',
        'fecha_expiracion_token',
        'ultimo_inicio_sesion',
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    public function obtenerUsuarioPorLogin($login)
    {
        return $this->select('usuarios.*, roles.nombre_rol')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol')
                    ->groupStart()
                        ->where('usuarios.nombre_usuario', $login)
                        ->orWhere('usuarios.correo_electronico', $login)
                    ->groupEnd()
                    ->first();
    }

    public function actualizarUltimoInicioSesion($idUsuario)
    {
        return $this->update($idUsuario, [
            'ultimo_inicio_sesion' => date('Y-m-d H:i:s')
        ]);
    }

    public function obtenerUsuarioPorCorreo($correo)
    {
        return $this->where('correo_electronico', $correo)->first();
    }

    public function obtenerUsuarioPorNombreUsuario($nombreUsuario)
    {
        return $this->where('nombre_usuario', $nombreUsuario)->first();
    }

    public function obtenerIdRolPorNombre($nombreRol)
    {
        $db = \Config\Database::connect();

        $rol = $db->table('roles')
                  ->where('nombre_rol', $nombreRol)
                  ->get()
                  ->getRowArray();

        return $rol ? $rol['id_rol'] : null;
    }

    public function crearUsuario($datos)
    {
        $this->insert($datos);
        return $this->insertID();
    }

    public function vincularUsuarioAPersona($tabla, $campoId, $valorId, $idUsuario)
    {
        $db = \Config\Database::connect();

        return $db->table($tabla)
                  ->where($campoId, $valorId)
                  ->update([
                      'id_usuario' => $idUsuario
                  ]);
    }

    public function buscarPersonaPendientePorCorreo($correo)
    {
        $db = \Config\Database::connect();

        $profesor = $db->table('profesores')
                       ->where('correo', $correo)
                       ->get()
                       ->getRowArray();

        if ($profesor) {
            return [
                'tabla'       => 'profesores',
                'campo_id'    => 'id_profesor',
                'valor_id'    => $profesor['id_profesor'],
                'id_usuario'  => $profesor['id_usuario'],
                'rol_sistema' => 'PROFESOR',
                'bloqueado_activacion'  => $profesor['bloqueado_activacion']
            ];
        }

        $estudiante = $db->table('estudiantes')
                         ->where('correo', $correo)
                         ->get()
                         ->getRowArray();

        if ($estudiante) {
            return [
                'tabla'       => 'estudiantes',
                'campo_id'    => 'id_estudiante',
                'valor_id'    => $estudiante['id_estudiante'],
                'id_usuario'  => $estudiante['id_usuario'],
                'rol_sistema' => 'ESTUDIANTE',
                'bloqueado_activacion'  => $estudiante['bloqueado_activacion']
            ];
        }

        $psicologo = $db->table('psicologos')
                        ->where('correo', $correo)
                        ->get()
                        ->getRowArray();

        if ($psicologo) {
            return [
                'tabla'       => 'psicologos',
                'campo_id'    => 'id_psicologo',
                'valor_id'    => $psicologo['id_psicologo'],
                'id_usuario'  => $psicologo['id_usuario'],
                'rol_sistema' => 'PSICOLOGIA',
                'bloqueado_activacion'  => $psicologo['bloqueado_activacion']
            ];
        }

        $administrativo = $db->table('administrativos')
                             ->where('correo', $correo)
                             ->get()
                             ->getRowArray();

        if ($administrativo) {
            return [
                'tabla'       => 'administrativos',
                'campo_id'    => 'id_administrativo',
                'valor_id'    => $administrativo['id_administrativo'],
                'id_usuario'  => $administrativo['id_usuario'],
                'rol_sistema' => 'ADMINISTRATIVO',
                'bloqueado_activacion'  => $administrativo['bloqueado_activacion']
            ];
        }

        return null;
    }

    public function guardarCodigoVerificacion($correo, $codigo)
    {
        $db = \Config\Database::connect();

        $db->table('verificacion_cuentas')
           ->where('correo', $correo)
           ->delete();

        return $db->table('verificacion_cuentas')->insert([
            'correo'           => $correo,
            'codigo'           => $codigo,
            'verificado'       => 0,
            'usado'            => 0,
            'intentos'         => 0,
            'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
        ]);
    }

    public function obtenerCodigoValido($correo, $codigo)
    {
        $db = \Config\Database::connect();

        return $db->table('verificacion_cuentas')
                  ->where('correo', $correo)
                  ->where('codigo', $codigo)
                  ->where('usado', 0)
                  ->where('fecha_expiracion >=', date('Y-m-d H:i:s'))
                  ->get()
                  ->getRowArray();
    }

    public function marcarCodigoVerificado($idVerificacion)
    {
        $db = \Config\Database::connect();

        return $db->table('verificacion_cuentas')
                  ->where('id_verificacion', $idVerificacion)
                  ->update([
                      'verificado' => 1
                  ]);
    }

    public function obtenerVerificacionExitosa($correo)
    {
        $db = \Config\Database::connect();

        return $db->table('verificacion_cuentas')
                  ->where('correo', $correo)
                  ->where('verificado', 1)
                  ->where('usado', 0)
                  ->where('fecha_expiracion >=', date('Y-m-d H:i:s'))
                  ->get()
                  ->getRowArray();
    }

    public function marcarCodigoUsado($idVerificacion)
    {
        $db = \Config\Database::connect();

        return $db->table('verificacion_cuentas')
                  ->where('id_verificacion', $idVerificacion)
                  ->update([
                      'usado' => 1
                  ]);
    }







    
    public function guardarCodigoRecuperacion($correo, $codigo)
{
    $db = \Config\Database::connect();

    $db->table('recuperacion_password')
       ->where('correo', $correo)
       ->delete();

    return $db->table('recuperacion_password')->insert([
        'correo'           => $correo,
        'codigo'           => $codigo,
        'verificado'       => 0,
        'usado'            => 0,
        'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
    ]);
}

public function obtenerCodigoRecuperacionValido($correo, $codigo)
{
    $db = \Config\Database::connect();

    return $db->table('recuperacion_password')
              ->where('correo', $correo)
              ->where('codigo', $codigo)
              ->where('usado', 0)
              ->where('fecha_expiracion >=', date('Y-m-d H:i:s'))
              ->get()
              ->getRowArray();
}

public function marcarCodigoRecuperacionVerificado($idRecuperacion)
{
    $db = \Config\Database::connect();

    return $db->table('recuperacion_password')
              ->where('id_recuperacion', $idRecuperacion)
              ->update([
                  'verificado' => 1
              ]);
}

public function obtenerRecuperacionVerificada($correo)
{
    $db = \Config\Database::connect();

    return $db->table('recuperacion_password')
              ->where('correo', $correo)
              ->where('verificado', 1)
              ->where('usado', 0)
              ->where('fecha_expiracion >=', date('Y-m-d H:i:s'))
              ->get()
              ->getRowArray();
}

public function marcarCodigoRecuperacionUsado($idRecuperacion)
{
    $db = \Config\Database::connect();

    return $db->table('recuperacion_password')
              ->where('id_recuperacion', $idRecuperacion)
              ->update([
                  'usado' => 1
              ]);
}

public function actualizarPasswordPorCorreo($correo, $hash)
{
    return $this->where('correo_electronico', $correo)
                ->set(['contrasena_hash' => $hash])
                ->update();
}

public function incrementarIntentosVerificacion($correo)
{
    $db = \Config\Database::connect();

    $registro = $db->table('verificacion_cuentas')
                   ->where('correo', $correo)
                   ->where('usado', 0)
                   ->orderBy('id_verificacion', 'DESC')
                   ->get()
                   ->getRowArray();

    if (!$registro) {
        return null;
    }

    $nuevoIntento = (int)$registro['intentos'] + 1;

    $db->table('verificacion_cuentas')
       ->where('id_verificacion', $registro['id_verificacion'])
       ->update([
           'intentos' => $nuevoIntento
       ]);

    $registro['intentos'] = $nuevoIntento;

    return $registro;
}

public function bloquearPersonaPorCorreo($correo)
{
    $db = \Config\Database::connect();

    $tablas = [
        'profesores',
        'estudiantes',
        'psicologos',
        'administrativos'
    ];

    foreach ($tablas as $tabla) {
        $persona = $db->table($tabla)
                      ->where('correo', $correo)
                      ->get()
                      ->getRowArray();

        if ($persona) {
            return $db->table($tabla)
                      ->where('correo', $correo)
                      ->update([
                          'bloqueado_activacion' => 1
                      ]);
        }
    }

    return false;
}
}
