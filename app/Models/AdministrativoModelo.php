<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministrativoModelo extends Model
{
     protected $table = 'administrativos';
    protected $primaryKey = 'id_administrativo';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'cargo',
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
            $builder->where('id_administrativo', $buscar);
        } else {
            $builder->like('nombres', $buscar)
                    ->orLike('apellidos', $buscar)
                    ->orLike('telefono', $buscar)
                    ->orLike('correo', $buscar)
                    ->orLike('cargo', $buscar)
                    ->orLike('fecha_creacion', $buscar);
        }

        if (
            strpos('bloqueado', $buscarMinuscula) !== false ||
            strpos('bloq', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('bloqueado_activacion', 1);
        }

        if (
            strpos('cuenta activada', $buscarMinuscula) !== false ||
            strpos('activada', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('bloqueado_activacion', 0);
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
            $builder->orWhere('id_administrativo IS NOT NULL');
        }

        $builder->groupEnd();

        return $builder;
    }

    public function listarAdministrativos()
    {
        return $this->orderBy('id_administrativo', 'DESC')
                    ->findAll();
    }

    public function buscarAdministrativos($buscar)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->orderBy('id_administrativo', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function listarAdministrativosPaginado($buscar, $limite, $offset, $orden, $direccion)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->orderBy($orden, $direccion)
                       ->limit($limite, $offset)
                       ->get()
                       ->getResultArray();
    }

    public function contarAdministrativos($buscar)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->countAllResults();
    }

    public function existeCorreo($correo, $idAdministrativo = null)
    {
        $builder = $this->where('correo', $correo);

        if ($idAdministrativo !== null) {
            $builder->where('id_administrativo !=', $idAdministrativo);
        }

        return $builder->first();
    }

    public function existeNombreCompleto($nombres, $apellidos, $idAdministrativo = null)
    {
        $builder = $this->where('nombres', $nombres)
                        ->where('apellidos', $apellidos);

        if ($idAdministrativo !== null) {
            $builder->where('id_administrativo !=', $idAdministrativo);
        }

        return $builder->first();
    }

    public function cambiarEstadoAdministrativoYUsuario($idAdministrativo, $estado)
    {
        $administrativo = $this->find($idAdministrativo);

        if (!$administrativo) {
            return false;
        }

        $this->update($idAdministrativo, [
            'estado' => $estado
        ]);

        if (!empty($administrativo['id_usuario'])) {
            $this->db->table('usuarios')
                ->where('id_usuario', $administrativo['id_usuario'])
                ->update([
                    'estado' => $estado
                ]);
        }

        return true;
    }
}

