<?php

namespace App\Models;

use CodeIgniter\Model;

class ComunicadosModelo extends Model
{
   protected $table = 'comunicados';
    protected $primaryKey = 'id_comunicado';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'titulo',
        'mensaje',
        'imagen',
        'fecha_creacion',
        'fecha_actualizacion',
        'estado'
    ];

    public function listarComunicados($idUsuario)
{
    $db = \Config\Database::connect();

    return $db->table('comunicados c')
        ->select('c.*, u.nombre_usuario, GROUP_CONCAT(r.nombre_rol SEPARATOR ", ") AS destinos')
        ->join('usuarios u', 'u.id_usuario = c.id_usuario')
        ->join('comunicado_destinos cd', 'cd.id_comunicado = c.id_comunicado', 'left')
        ->join('roles r', 'r.id_rol = cd.id_rol', 'left')
        ->where('c.id_usuario', $idUsuario)
        ->groupBy('c.id_comunicado')
        ->orderBy('c.id_comunicado', 'ASC')
        ->get()
        ->getResultArray();
}

    public function obtenerComunicadoPorId($idComunicado)
    {
        return $this->where('id_comunicado', $idComunicado)->first();
    }

    public function obtenerRoles()
    {
        $db = \Config\Database::connect();

        return $db->table('roles')
            ->orderBy('nombre_rol', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function obtenerDestinosPorComunicado($idComunicado)
    {
        $db = \Config\Database::connect();

        $resultados = $db->table('comunicado_destinos')
            ->select('id_rol')
            ->where('id_comunicado', $idComunicado)
            ->get()
            ->getResultArray();

        return array_column($resultados, 'id_rol');
    }

    public function guardarDestinos($idComunicado, $rolesDestino)
    {
        $db = \Config\Database::connect();

        $db->table('comunicado_destinos')
            ->where('id_comunicado', $idComunicado)
            ->delete();

        if (!empty($rolesDestino)) {
            foreach ($rolesDestino as $idRol) {
                $db->table('comunicado_destinos')->insert([
                    'id_comunicado' => $idComunicado,
                    'id_rol'        => $idRol
                ]);
            }
        }
    }

    public function cambiarEstado($idComunicado, $estado)
    {
        return $this->update($idComunicado, [
            'estado' => $estado
        ]);
    }

    public function listarComunicadosPorRol($idRol)
{
    $db = \Config\Database::connect();

    return $db->table('comunicados c')
        ->select('c.*, u.nombre_usuario, r.nombre_rol, u.nombre_usuario AS nombre_publicador')
        ->join('usuarios u', 'u.id_usuario = c.id_usuario')
        ->join('roles r', 'r.id_rol = u.id_rol')
        ->join('comunicado_destinos cd', 'cd.id_comunicado = c.id_comunicado')
        ->where('cd.id_rol', $idRol)
        ->where('c.estado', 1)
        ->orderBy('c.id_comunicado', 'DESC')
        ->get()
        ->getResultArray();
}

public function obtenerComunicadoDetalle($idComunicado, $idRol)
{
    $db = \Config\Database::connect();

    return $db->table('comunicados c')
        ->select('c.*, u.nombre_usuario, r.nombre_rol, u.nombre_usuario AS nombre_publicador')
        ->join('usuarios u', 'u.id_usuario = c.id_usuario')
        ->join('roles r', 'r.id_rol = u.id_rol')
        ->join('comunicado_destinos cd', 'cd.id_comunicado = c.id_comunicado')
        ->where('c.id_comunicado', $idComunicado)
        ->where('cd.id_rol', $idRol)
        ->where('c.estado', 1)
        ->get()
        ->getRowArray();
}

public function buscarComunicados($idUsuario, $buscar)
{
    $buscar = trim($buscar);
    $buscarMinuscula = strtolower($buscar);

    $db = \Config\Database::connect();

    $builder = $db->table('comunicados c')
        ->select('c.*, u.nombre_usuario, GROUP_CONCAT(r.nombre_rol SEPARATOR ", ") AS destinos')
        ->join('usuarios u', 'u.id_usuario = c.id_usuario')
        ->join('comunicado_destinos cd', 'cd.id_comunicado = c.id_comunicado', 'left')
        ->join('roles r', 'r.id_rol = cd.id_rol', 'left')
        ->where('c.id_usuario', $idUsuario);

    // Estado: Activo / Desactivar
    if (
        $buscarMinuscula === 'a' ||
        $buscarMinuscula === 'ac' ||
        $buscarMinuscula === 'act' ||
        $buscarMinuscula === 'acti' ||
        $buscarMinuscula === 'activ' ||
        $buscarMinuscula === 'activo' ||
        $buscarMinuscula === 'des' ||
        $buscarMinuscula === 'desa' ||
        $buscarMinuscula === 'desact' ||
        $buscarMinuscula === 'desactivar'
    ) {
        return $builder->where('c.estado', 1)
                       ->groupBy('c.id_comunicado')
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Estado: Inactivo / Activar
    if (
        $buscarMinuscula === 'i' ||
        $buscarMinuscula === 'in' ||
        $buscarMinuscula === 'ina' ||
        $buscarMinuscula === 'inac' ||
        $buscarMinuscula === 'inact' ||
        $buscarMinuscula === 'inactivo' ||
        $buscarMinuscula === 'activar'
    ) {
        return $builder->where('c.estado', 0)
                       ->groupBy('c.id_comunicado')
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Imagen
    if (
        $buscarMinuscula === 'sin' ||
        $buscarMinuscula === 'sin imagen'
    ) {
        return $builder->groupStart()
                            ->where('c.imagen', null)
                            ->orWhere('c.imagen', '')
                       ->groupEnd()
                       ->groupBy('c.id_comunicado')
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    if (
        $buscarMinuscula === 'im' ||
        $buscarMinuscula === 'ima' ||
        $buscarMinuscula === 'imagen'
    ) {
        return $builder->where('c.imagen IS NOT NULL')
                       ->where('c.imagen !=', '')
                       ->groupBy('c.id_comunicado')
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Botón editar
    if (
        $buscarMinuscula === 'e' ||
        $buscarMinuscula === 'ed' ||
        $buscarMinuscula === 'edi' ||
        $buscarMinuscula === 'edit' ||
        $buscarMinuscula === 'editar'
    ) {
        return $builder->groupBy('c.id_comunicado')
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // Campos normales
    return $builder->groupStart()
                    ->like('c.id_comunicado', $buscar)
                    ->orLike('c.titulo', $buscar)
                    ->orLike('c.mensaje', $buscar)
                    ->orLike('u.nombre_usuario', $buscar)
                    ->orLike('r.nombre_rol', $buscar)
                    ->orLike('c.fecha_creacion', $buscar)
                    ->orLike('c.fecha_actualizacion', $buscar)
                  ->groupEnd()
                  ->groupBy('c.id_comunicado')
                  ->orderBy('c.id_comunicado', 'DESC')
                  ->get()
                  ->getResultArray();
}


public function buscarComunicadosPorRol($idRol, $buscar)
{
    $buscar = trim($buscar);
    $buscarMinuscula = strtolower($buscar);

    $db = \Config\Database::connect();

    $builder = $db->table('comunicados c')
        ->select('c.*, u.nombre_usuario, r.nombre_rol, u.nombre_usuario AS nombre_publicador')
        ->join('usuarios u', 'u.id_usuario = c.id_usuario')
        ->join('roles r', 'r.id_rol = u.id_rol')
        ->join('comunicado_destinos cd', 'cd.id_comunicado = c.id_comunicado')
        ->where('cd.id_rol', $idRol)
        ->where('c.estado', 1);

    if (
        $buscarMinuscula === 'v' ||
        $buscarMinuscula === 've' ||
        $buscarMinuscula === 'ver' ||
        $buscarMinuscula === 'publicacion' ||
        $buscarMinuscula === 'publicación' ||
        $buscarMinuscula === 'ver publicacion' ||
        $buscarMinuscula === 'ver publicación'
    ) {
        return $builder->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    if (
        $buscarMinuscula === 'im' ||
        $buscarMinuscula === 'ima' ||
        $buscarMinuscula === 'imagen'
    ) {
        return $builder->where('c.imagen IS NOT NULL')
                       ->where('c.imagen !=', '')
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    if (
        $buscarMinuscula === 'sin' ||
        $buscarMinuscula === 'sin imagen'
    ) {
        return $builder->groupStart()
                            ->where('c.imagen', null)
                            ->orWhere('c.imagen', '')
                       ->groupEnd()
                       ->orderBy('c.id_comunicado', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    return $builder->groupStart()
                    ->like('c.id_comunicado', $buscar)
                    ->orLike('c.titulo', $buscar)
                    ->orLike('c.mensaje', $buscar)
                    ->orLike('u.nombre_usuario', $buscar)
                    ->orLike('r.nombre_rol', $buscar)
                    ->orLike('c.fecha_creacion', $buscar)
                    ->orLike('c.fecha_actualizacion', $buscar)
                  ->groupEnd()
                  ->orderBy('c.id_comunicado', 'DESC')
                  ->get()
                  ->getResultArray();
}
}
