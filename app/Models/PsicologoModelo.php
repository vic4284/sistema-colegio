<?php

namespace App\Models;

use CodeIgniter\Model;

class PsicologoModelo extends Model
{
     protected $table = 'psicologos';
    protected $primaryKey = 'id_psicologo';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'numero_registro',
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
            $builder->where('id_psicologo', $buscar);
        } else {
            $builder->like('nombres', $buscar)
                    ->orLike('apellidos', $buscar)
                    ->orLike('telefono', $buscar)
                    ->orLike('correo', $buscar)
                    ->orLike('numero_registro', $buscar)
                    ->orLike('fecha_creacion', $buscar);
        }

        if (
            strpos('bloqueado', $buscarMinuscula) !== false ||
            strpos('bloq', $buscarMinuscula) !== false
        ) {
            $builder->orWhere('bloqueado_activacion', 1);
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
            $builder->orWhere('id_psicologo IS NOT NULL');
        }

        $builder->groupEnd();

        return $builder;
    }

    public function listarPsicologos()
    {
        return $this->orderBy('id_psicologo', 'DESC')
                    ->findAll();
    }

    public function buscarPsicologos($buscar)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->orderBy('id_psicologo', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function listarPsicologosPaginado($buscar, $limite, $offset, $orden, $direccion)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->orderBy($orden, $direccion)
                       ->limit($limite, $offset)
                       ->get()
                       ->getResultArray();
    }

    public function contarPsicologos($buscar)
    {
        $builder = $this->builder();
        $builder = $this->aplicarBusqueda($builder, $buscar);

        return $builder->countAllResults();
    }

    public function existeCorreo($correo, $idPsicologo = null)
    {
        $builder = $this->where('correo', $correo);

        if ($idPsicologo !== null) {
            $builder->where('id_psicologo !=', $idPsicologo);
        }

        return $builder->first();
    }

    public function existeNombreCompleto($nombres, $apellidos, $idPsicologo = null)
    {
        $builder = $this->where('nombres', $nombres)
                        ->where('apellidos', $apellidos);

        if ($idPsicologo !== null) {
            $builder->where('id_psicologo !=', $idPsicologo);
        }

        return $builder->first();
    }

    public function existeNumeroRegistro($numeroRegistro, $idPsicologo = null)
    {
        $builder = $this->where('numero_registro', $numeroRegistro);

        if ($idPsicologo !== null) {
            $builder->where('id_psicologo !=', $idPsicologo);
        }

        return $builder->first();
    }

    public function cambiarEstadoPsicologoYUsuario($idPsicologo, $estado)
    {
        $psicologo = $this->find($idPsicologo);

        if (!$psicologo) {
            return false;
        }

        $this->update($idPsicologo, [
            'estado' => $estado
        ]);

        if (!empty($psicologo['id_usuario'])) {
            $this->db->table('usuarios')
                ->where('id_usuario', $psicologo['id_usuario'])
                ->update([
                    'estado' => $estado
                ]);
        }

        return true;
    }
}
