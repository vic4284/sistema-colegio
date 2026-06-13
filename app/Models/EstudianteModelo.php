<?php

namespace App\Models;

use CodeIgniter\Model;

class EstudianteModelo extends Model
{
     protected $table = 'estudiantes';
    protected $primaryKey = 'id_estudiante';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'direccion',
        'estado',
         'bloqueado_activacion'
    ];


    public function cambiarEstadoEstudianteYUsuario($idEstudiante, $estado)
{
    $estudiante = $this->find($idEstudiante);

    if (!$estudiante) {
        return false;
    }

    $this->update($idEstudiante, [
        'estado' => $estado
    ]);

    if (!empty($estudiante['id_usuario'])) {
        $db = \Config\Database::connect();

        $db->table('usuarios')
           ->where('id_usuario', $estudiante['id_usuario'])
           ->update([
               'estado' => $estado
           ]);
    }

    return true;
}

public function buscarEstudiantes($buscar)
{
    $buscar = trim($buscar);
    $buscarMinuscula = strtolower($buscar);

    $builder = $this->builder();

    // ESTADO / ACCIONES
    if (
        $buscarMinuscula === 'ac' ||
        $buscarMinuscula === 'act' ||
        $buscarMinuscula === 'acti' ||
        $buscarMinuscula === 'activ' ||
        $buscarMinuscula === 'activo' ||
        $buscarMinuscula === 'des' ||
        $buscarMinuscula === 'desact' ||
        $buscarMinuscula === 'desactivar'
    ) {
        return $builder->where('estado', 1)
                       ->orderBy('id_estudiante', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    if (
        $buscarMinuscula === 'in' ||
        $buscarMinuscula === 'ina' ||
        $buscarMinuscula === 'inac' ||
        $buscarMinuscula === 'inact' ||
        $buscarMinuscula === 'inactivo' ||
        $buscarMinuscula === 'activar'
    ) {
        return $builder->where('estado', 0)
                       ->orderBy('id_estudiante', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // USUARIO VINCULADO
    if (
        $buscarMinuscula === 'pe' ||
        $buscarMinuscula === 'pen' ||
        $buscarMinuscula === 'pend' ||
        $buscarMinuscula === 'pendiente'
    ) {
        return $builder->where('id_usuario', null)
                       ->where('bloqueado_activacion', 0)
                       ->orderBy('id_estudiante', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    if (
        $buscarMinuscula === 'cu' ||
        $buscarMinuscula === 'cue' ||
        $buscarMinuscula === 'cuenta' ||
        $buscarMinuscula === 'activada' ||
        $buscarMinuscula === 'cuenta activada'
    ) {
        return $builder->where('id_usuario IS NOT NULL')
                       ->where('bloqueado_activacion', 0)
                       ->orderBy('id_estudiante', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    if (
        $buscarMinuscula === 'blo' ||
        $buscarMinuscula === 'bloq' ||
        $buscarMinuscula === 'bloqueado'
    ) {
        return $builder->where('bloqueado_activacion', 1)
                       ->orderBy('id_estudiante', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // BOTÓN EDITAR
    if (
        $buscarMinuscula === 'ed' ||
        $buscarMinuscula === 'edi' ||
        $buscarMinuscula === 'edit' ||
        $buscarMinuscula === 'editar'
    ) {
        return $builder->orderBy('id_estudiante', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    // CAMPOS NORMALES
    return $builder->groupStart()
                    ->like('id_estudiante', $buscar)
                    ->orLike('nombres', $buscar)
                    ->orLike('apellidos', $buscar)
                    ->orLike('telefono', $buscar)
                    ->orLike('correo', $buscar)
                    ->orLike('direccion', $buscar)
                    ->orLike('fecha_creacion', $buscar)
                   ->groupEnd()
                   ->orderBy('id_estudiante', 'DESC')
                   ->get()
                   ->getResultArray();
}

public function existeCorreo($correo, $idEstudiante = null)
{
    $builder = $this->where('correo', $correo);

    if ($idEstudiante !== null) {
        $builder->where('id_estudiante !=', $idEstudiante);
    }

    return $builder->first();
}

public function existeNombreCompleto($nombres, $apellidos, $idEstudiante = null)
{
    $builder = $this->where('nombres', $nombres)
                    ->where('apellidos', $apellidos);

    if ($idEstudiante !== null) {
        $builder->where('id_estudiante !=', $idEstudiante);
    }

    return $builder->first();
}
}
