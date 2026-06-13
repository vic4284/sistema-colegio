<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfesorModelo extends Model
{
    protected $table = 'profesores';
    protected $primaryKey = 'id_profesor';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'especialidad',
        'estado',
        'bloqueado_activacion'
    ];

    public function cambiarEstadoProfesorYUsuario($idProfesor, $estado)
{
    $profesor = $this->find($idProfesor);

    if (!$profesor) {
        return false;
    }

    $this->update($idProfesor, [
        'estado' => $estado
    ]);

    if (!empty($profesor['id_usuario'])) {
        $db = \Config\Database::connect();

        $db->table('usuarios')
           ->where('id_usuario', $profesor['id_usuario'])
           ->update([
               'estado' => $estado
           ]);
    }

    return true;
}

public function buscarProfesores($buscar)
{
    $buscar = trim($buscar);
    $buscarMinuscula = strtolower($buscar);

    $builder = $this->builder();

    $builder->groupStart();

        $builder->like('id_profesor', $buscar)
                ->orLike('nombres', $buscar)
                ->orLike('apellidos', $buscar)
                ->orLike('telefono', $buscar)
                ->orLike('correo', $buscar)
                ->orLike('especialidad', $buscar)
                ->orLike('fecha_creacion', $buscar);

        if (
            $buscarMinuscula === 'pendiente' ||
            $buscarMinuscula === 'pend'
        ) {
            $builder->orWhere('id_usuario', null)
                    ->where('bloqueado_activacion', 0);
        }

        if (
            $buscarMinuscula === 'cuenta activada' ||
            $buscarMinuscula === 'activada'
        ) {
            $builder->orWhere('id_usuario IS NOT NULL')
                    ->where('bloqueado_activacion', 0);
        }

        if (
            $buscarMinuscula === 'bloqueado' ||
            $buscarMinuscula === 'bloq'
        ) {
            $builder->orWhere('bloqueado_activacion', 1);
        }

        if (
            $buscarMinuscula === 'inactivo' ||
            $buscarMinuscula === 'inact' ||
            $buscarMinuscula === 'activar'
        ) {
            $builder->orWhere('estado', 0);
        } elseif (
            $buscarMinuscula === 'activo' ||
            $buscarMinuscula === 'activ' ||
            $buscarMinuscula === 'desactivar'
        ) {
            $builder->orWhere('estado', 1);
        }

        if ($buscarMinuscula === 'editar') {
            $builder->orWhere('id_profesor IS NOT NULL');
        }

    $builder->groupEnd();

    return $builder->orderBy('id_profesor', 'DESC')
                   ->get()
                   ->getResultArray();
}
public function existeCorreo($correo, $idProfesor = null)
{
    $builder = $this->where('correo', $correo);

    if ($idProfesor !== null) {
        $builder->where('id_profesor !=', $idProfesor);
    }

    return $builder->first();
}

public function existeNombreCompleto($nombres, $apellidos, $idProfesor = null)
{
    $builder = $this->where('nombres', $nombres)
                    ->where('apellidos', $apellidos);

    if ($idProfesor !== null) {
        $builder->where('id_profesor !=', $idProfesor);
    }

    return $builder->first();
}

}
