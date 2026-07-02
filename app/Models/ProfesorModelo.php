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

    private function aplicarBusqueda($builder, $buscar)
    {
        $buscar = trim($buscar);
        $buscarMinuscula = strtolower($buscar);

        if ($buscar === '') {
            return $builder;
        }

        $builder->groupStart();

        if (is_numeric($buscar) && strlen($buscar) <= 3) {
            $builder->where('id_profesor', $buscar);
        } else {
            $builder->like('nombres', $buscar)
                    ->orLike('apellidos', $buscar)
                    ->orLike('telefono', $buscar)
                    ->orLike('correo', $buscar)
                    ->orLike('especialidad', $buscar)
                    ->orLike('fecha_creacion', $buscar);
        }

        if (
            strpos('pendiente', $buscarMinuscula) !== false ||
            strpos('pend', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('id_usuario', null)
                    ->where('bloqueado_activacion', 0);
        }

        if (
            strpos('cuenta activada', $buscarMinuscula) !== false ||
            strpos('activada', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('id_usuario IS NOT NULL')
                    ->where('bloqueado_activacion', 0);
        }

        if (
            strpos('bloqueado', $buscarMinuscula) !== false ||
            strpos('bloq', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('bloqueado_activacion', 1);
        }

        if (
            $buscarMinuscula === 'inactivo' ||
            $buscarMinuscula === 'inact' ||
            $buscarMinuscula === 'inacti' ||
            $buscarMinuscula === 'activar'
        ) {
            $builder->orWhere('estado', 0);
        } elseif (
            $buscarMinuscula === 'activo' ||
            $buscarMinuscula === 'activ' ||
            $buscarMinuscula === 'desactivar' ||
            $buscarMinuscula === 'desact'
        ) {
            $builder->orWhere('estado', 1);
        }

        if (strpos('editar', $buscarMinuscula) !== false) {
            $builder->orWhere('id_profesor IS NOT NULL');
        }

        $builder->groupEnd();

        return $builder;
    }

    public function listarProfesores()
    {
        return $this->orderBy('id_profesor', 'DESC')
                    ->findAll();
    }

    public function buscarProfesores($buscar)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->orderBy('id_profesor', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function listarProfesoresPaginado($buscar, $limite, $offset, $orden, $direccion)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->orderBy($orden, $direccion)
                       ->limit($limite, $offset)
                       ->get()
                       ->getResultArray();
    }

    public function contarProfesores($buscar)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->countAllResults();
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
            $this->db->table('usuarios')
                ->where('id_usuario', $profesor['id_usuario'])
                ->update([
                    'estado' => $estado
                ]);
        }

        return true;
    }

}
