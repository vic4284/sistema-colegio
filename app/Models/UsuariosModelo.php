<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModelo extends Model
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

    public function listarUsuarios()
    {
        return $this->select('usuarios.*, roles.nombre_rol')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol')
                    ->orderBy('usuarios.id_usuario', 'ASC')
                    ->findAll();
    }

    public function obtenerUsuarioPorId($idUsuario)
    {
        return $this->select('usuarios.*, roles.nombre_rol')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol')
                    ->where('usuarios.id_usuario', $idUsuario)
                    ->first();
    }

    public function cambiarEstadoUsuario($idUsuario, $nuevoEstado)
    {
        return $this->update($idUsuario, [
            'estado' => $nuevoEstado
        ]);
    }

    public function existeNombreUsuario($nombreUsuario, $idExcluir = null)
    {
        $builder = $this->where('nombre_usuario', $nombreUsuario);

        if ($idExcluir !== null) {
            $builder->where('id_usuario !=', $idExcluir);
        }

        return $builder->first() !== null;
    }

    public function existeCorreoElectronico($correo, $idExcluir = null)
    {
        $builder = $this->where('correo_electronico', $correo);

        if ($idExcluir !== null) {
            $builder->where('id_usuario !=', $idExcluir);
        }

        return $builder->first() !== null;
    }


    public function cambiarEstadoUsuarioYPersona($idUsuario, $estado)
{
    $usuario = $this->obtenerUsuarioPorId($idUsuario);

    if (!$usuario) {
        return false;
    }

    $this->update($idUsuario, [
        'estado' => $estado
    ]);

    $db = \Config\Database::connect();

    $tablas = [
        'administrativos',
        'profesores',
        'estudiantes',
        'psicologos'
    ];

    foreach ($tablas as $tabla) {
        $db->table($tabla)
           ->where('id_usuario', $idUsuario)
           ->update([
               'estado' => $estado
           ]);
    }

    return true;
}
    public function buscarUsuarios($buscar)
{
    $buscar = trim($buscar);
    $buscarMinuscula = strtolower($buscar);

    $builder = $this->select('usuarios.*, roles.nombre_rol')
                    ->join('roles', 'roles.id_rol = usuarios.id_rol');

    $builder->groupStart();

        if (is_numeric($buscar)) {
            $builder->where('usuarios.id_usuario', $buscar);
        }

        $builder->orLike('usuarios.nombre_usuario', $buscar)
                ->orLike('usuarios.correo_electronico', $buscar)
                ->orLike('roles.nombre_rol', $buscar)
                ->orLike('usuarios.ultimo_inicio_sesion', $buscar)
                ->orLike('usuarios.fecha_creacion', $buscar);

        if (
    strpos('inactivo', $buscarMinuscula) !== false ||
    strpos('activar', $buscarMinuscula) !== false
) {
    $builder->orWhere('usuarios.estado', 0);
} elseif (
    strpos('activo', $buscarMinuscula) !== false ||
    strpos('desactivar', $buscarMinuscula) !== false
) {
    $builder->orWhere('usuarios.estado', 1);
}

        if (strpos('editar', $buscarMinuscula) !== false) {
            $builder->orWhere('usuarios.id_usuario IS NOT NULL');
        }

        if (
            strpos('sin imagen', $buscarMinuscula) !== false ||
            strpos('sin ima', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('usuarios.imagen', null)
                    ->orWhere('usuarios.imagen', '');
        }

        if (
            strpos('imagen de usuario', $buscarMinuscula) !== false ||
            strpos('imagen', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('usuarios.imagen IS NOT NULL')
                    ->where('usuarios.imagen !=', '');
        }

    $builder->groupEnd();

    return $builder->orderBy('usuarios.id_usuario', 'DESC')
                   ->findAll();
}
}