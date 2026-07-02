<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdministrativoModelo;

class ControladorAdministrativos extends BaseController
{
       protected $administrativoModelo;

    public function __construct()
    {
        $this->administrativoModelo = new AdministrativoModelo();
    }

    private function esAdministrativo()
    {
        return session()->get('logueado') && session()->get('rol') === 'ADMINISTRATIVO';
    }

    private function validarAccesoModulo()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        return null;
    }

    private function validarAccionAdministrativo()
    {
        if (!$this->esAdministrativo()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        return null;
    }

    private function validarDatosAdministrativo($nombres, $apellidos, $telefono, $correo, $cargo)
    {
        if ($nombres === '') return 'Los nombres son obligatorios.';
        if (mb_strlen($nombres) < 2) return 'Los nombres deben tener al menos 2 caracteres.';
        if (mb_strlen($nombres) > 50) return 'Los nombres no deben superar los 50 caracteres.';

        if ($apellidos === '') return 'Los apellidos son obligatorios.';
        if (mb_strlen($apellidos) < 2) return 'Los apellidos deben tener al menos 2 caracteres.';
        if (mb_strlen($apellidos) > 50) return 'Los apellidos no deben superar los 50 caracteres.';

        if ($telefono === '') return 'El teléfono es obligatorio.';
        if (!preg_match('/^[0-9]+$/', $telefono)) return 'El teléfono solo debe contener números.';
        if (mb_strlen($telefono) < 7) return 'El teléfono debe tener al menos 7 dígitos.';
        if (mb_strlen($telefono) > 15) return 'El teléfono no debe superar los 15 dígitos.';

        if ($correo === '') return 'El correo es obligatorio.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return 'Debe ingresar un correo válido.';
        if (mb_strlen($correo) > 100) return 'El correo no debe superar los 100 caracteres.';

        if (mb_strlen($cargo) > 80) return 'El cargo no debe superar los 80 caracteres.';

        return null;
    }

    private function obtenerDatosFormulario()
    {
        return [
            'nombres'   => trim($this->request->getPost('nombres') ?? ''),
            'apellidos' => trim($this->request->getPost('apellidos') ?? ''),
            'telefono'  => trim($this->request->getPost('telefono') ?? ''),
            'correo'    => trim($this->request->getPost('correo') ?? ''),
            'cargo'     => trim($this->request->getPost('cargo') ?? '')
        ];
    }

    private function redirigirErrorInsertar($mensaje)
    {
        return redirect()->to(base_url('/administrativos#modal-insertar-administrativo'))
            ->with('error_formulario', $mensaje)
            ->with('modal_formulario', 'insertar')
            ->withInput();
    }

    private function redirigirErrorEditar($id, $mensaje)
    {
        return redirect()->to(base_url('/administrativos#modal-editar-' . $id))
            ->with('error_formulario', $mensaje)
            ->with('modal_formulario', 'editar')
            ->with('id_modal_formulario', $id)
            ->withInput();
    }

    public function index()
    {
        $acceso = $this->validarAccesoModulo();

        if ($acceso !== null) {
            return $acceso;
        }

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        $porPagina = (int)($this->request->getGet('por_pagina') ?? 10);
        $pagina = (int)($this->request->getGet('pagina') ?? 1);

        if (!in_array($porPagina, [10, 25, 50, 100])) {
            $porPagina = 10;
        }

        if ($pagina < 1) {
            $pagina = 1;
        }

        $columnasPermitidas = [
            'id_administrativo',
            'nombres',
            'apellidos',
            'telefono',
            'correo',
            'cargo',
            'bloqueado_activacion',
            'estado',
            'fecha_creacion'
        ];

        $orden = $this->request->getGet('orden') ?? 'id_administrativo';
        $direccion = strtolower($this->request->getGet('direccion') ?? 'desc');

        if (!in_array($orden, $columnasPermitidas)) {
            $orden = 'id_administrativo';
        }

        if (!in_array($direccion, ['asc', 'desc'])) {
            $direccion = 'desc';
        }

        $totalRegistros = $this->administrativoModelo->contarAdministrativos($buscar);
        $totalPaginas = (int)ceil($totalRegistros / $porPagina);

        if ($totalPaginas > 0 && $pagina > $totalPaginas) {
            $pagina = $totalPaginas;
        }

        $offset = ($pagina - 1) * $porPagina;

        $administrativos = $this->administrativoModelo->listarAdministrativosPaginado(
            $buscar,
            $porPagina,
            $offset,
            $orden,
            $direccion
        );

        $desde = $totalRegistros > 0 ? $offset + 1 : 0;
        $hasta = min($offset + $porPagina, $totalRegistros);

        return view('administrativos/index', [
            'administrativos' => $administrativos,
            'buscar' => $buscar,
            'porPagina' => $porPagina,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros,
            'desde' => $desde,
            'hasta' => $hasta,
            'orden' => $orden,
            'direccion' => $direccion
        ]);
    }

    public function insertar()
    {
        $acceso = $this->validarAccionAdministrativo();

        if ($acceso !== null) {
            return $acceso;
        }

        $datos = $this->obtenerDatosFormulario();

        $error = $this->validarDatosAdministrativo(
            $datos['nombres'],
            $datos['apellidos'],
            $datos['telefono'],
            $datos['correo'],
            $datos['cargo']
        );

        if ($error !== null) {
            return $this->redirigirErrorInsertar($error);
        }

        if ($this->administrativoModelo->existeCorreo($datos['correo'])) {
            return $this->redirigirErrorInsertar('Ya existe un administrativo registrado con ese correo.');
        }

        if ($this->administrativoModelo->existeNombreCompleto($datos['nombres'], $datos['apellidos'])) {
            return $this->redirigirErrorInsertar('Ya existe un administrativo registrado con ese nombre completo.');
        }

        $this->administrativoModelo->insert([
            'id_usuario' => null,
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono'  => $datos['telefono'],
            'correo'    => $datos['correo'],
            'cargo'     => $datos['cargo'],
            'estado'    => 1
        ]);

        return redirect()->to(base_url('/administrativos'))
            ->with('success', 'Administrativo registrado correctamente.');
    }

    public function actualizar($id = null)
    {
        $acceso = $this->validarAccionAdministrativo();

        if ($acceso !== null) {
            return $acceso;
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'ID de administrativo no válido.');
        }

        $administrativo = $this->administrativoModelo->find($id);

        if (!$administrativo) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Administrativo no encontrado.');
        }

        $datos = $this->obtenerDatosFormulario();

        $error = $this->validarDatosAdministrativo(
            $datos['nombres'],
            $datos['apellidos'],
            $datos['telefono'],
            $datos['correo'],
            $datos['cargo']
        );

        if ($error !== null) {
            return $this->redirigirErrorEditar($id, $error);
        }

        if ($this->administrativoModelo->existeCorreo($datos['correo'], $id)) {
            return $this->redirigirErrorEditar($id, 'Ya existe otro administrativo registrado con ese correo.');
        }

        if ($this->administrativoModelo->existeNombreCompleto($datos['nombres'], $datos['apellidos'], $id)) {
            return $this->redirigirErrorEditar($id, 'Ya existe otro administrativo registrado con ese nombre completo.');
        }

        $this->administrativoModelo->update($id, [
            'nombres'              => $datos['nombres'],
            'apellidos'            => $datos['apellidos'],
            'telefono'             => $datos['telefono'],
            'correo'               => $datos['correo'],
            'cargo'                => $datos['cargo'],
            'bloqueado_activacion' => $this->request->getPost('bloqueado_activacion') ? 0 : $this->request->getPost('bloqueado_actual')
        ]);

        return redirect()->to(base_url('/administrativos'))
            ->with('success', 'Administrativo actualizado correctamente.');
    }

    public function activar($id = null)
    {
        return $this->cambiarEstado($id, 1, 'activado');
    }

    public function desactivar($id = null)
    {
        return $this->cambiarEstado($id, 0, 'desactivado');
    }

    private function cambiarEstado($id, $estado, $texto)
    {
        $acceso = $this->validarAccionAdministrativo();

        if ($acceso !== null) {
            return $acceso;
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'ID de administrativo no válido.');
        }

        $actualizado = $this->administrativoModelo->cambiarEstadoAdministrativoYUsuario($id, $estado);

        if (!$actualizado) {
            return redirect()->to(base_url('/administrativos'))
                ->with('error', 'Administrativo no encontrado.');
        }

        return redirect()->to(base_url('/administrativos'))
            ->with('success', 'Administrativo ' . $texto . ' correctamente.');
    }
}