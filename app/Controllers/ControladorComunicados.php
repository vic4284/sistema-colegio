<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ComunicadosModelo;

class ControladorComunicados extends BaseController
{
    private function puedeGestionarComunicados()
    {
        return session()->get('logueado') &&
            in_array(session()->get('rol'), ['ADMINISTRATIVO', 'PROFESOR', 'PSICOLOGIA']);
    }

    private function validarDatosComunicado($titulo, $mensaje, $rolesDestino, $imagen = null)
    {
        if ($titulo === '') {
            return 'El título es obligatorio.';
        }

        if (mb_strlen($titulo) < 3) {
            return 'El título debe tener al menos 3 caracteres.';
        }

        if (mb_strlen($titulo) > 100) {
            return 'El título no debe superar los 100 caracteres.';
        }

        if ($mensaje === '') {
            return 'El mensaje es obligatorio.';
        }

        if (mb_strlen($mensaje) < 5) {
            return 'El mensaje debe tener al menos 5 caracteres.';
        }

        if (mb_strlen($mensaje) > 500) {
            return 'El mensaje no debe superar los 500 caracteres.';
        }

        if (empty($rolesDestino)) {
            return 'Debe seleccionar al menos un rol destino.';
        }

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $extension = strtolower($imagen->getExtension());
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($extension, $permitidas)) {
                return 'Solo se permiten imágenes JPG, JPEG, PNG o WEBP.';
            }

            if ($imagen->getSize() > 2 * 1024 * 1024) {
                return 'La imagen no debe superar los 2 MB.';
            }
        }

        return null;
    }

    public function index()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if (!$this->puedeGestionarComunicados()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para acceder a este módulo.');
        }

        $modelo = new ComunicadosModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $comunicados = $modelo->buscarComunicados(session()->get('id_usuario'), $buscar);
        } else {
            $comunicados = $modelo->listarComunicados(session()->get('id_usuario'));
        }

        foreach ($comunicados as &$comunicado) {
            $comunicado['roles_destino'] = $modelo->obtenerDestinosPorComunicado($comunicado['id_comunicado']);
        }

        return view('Comunicados/index', [
            'comunicados' => $comunicados,
            'roles'       => $modelo->obtenerRoles()
        ]);
    }

    public function guardar()
    {
        if (!$this->puedeGestionarComunicados()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        $titulo       = trim($this->request->getPost('titulo') ?? '');
        $mensaje      = trim($this->request->getPost('mensaje') ?? '');
        $rolesDestino = $this->request->getPost('roles_destino');
        $imagen       = $this->request->getFile('imagen');

        $error = $this->validarDatosComunicado($titulo, $mensaje, $rolesDestino, $imagen);

        if ($error !== null) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', $error)
                ->withInput();
        }

        $nombreImagen = null;

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $nombreImagen = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/assets/img/comunicados', $nombreImagen);
        }

        $modelo = new ComunicadosModelo();

        $modelo->insert([
            'id_usuario'          => session()->get('id_usuario'),
            'titulo'              => $titulo,
            'mensaje'             => $mensaje,
            'imagen'              => $nombreImagen,
            'fecha_actualizacion' => null,
            'estado'              => 1
        ]);

        $idComunicado = $modelo->insertID();
        $modelo->guardarDestinos($idComunicado, $rolesDestino);

        return redirect()->to(base_url('/comunicados'))
            ->with('success', 'Comunicado registrado correctamente.');
    }

    public function actualizar($idComunicado = null)
    {
        if (!$this->puedeGestionarComunicados()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idComunicado === null || !is_numeric($idComunicado)) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', 'ID de comunicado no válido.');
        }

        $modelo = new ComunicadosModelo();
        $comunicado = $modelo->obtenerComunicadoPorId($idComunicado);

        if (!$comunicado) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', 'Comunicado no encontrado.');
        }

        if ((int)$comunicado['id_usuario'] !== (int)session()->get('id_usuario')) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', 'No tiene permisos para modificar este comunicado.');
        }

        $titulo       = trim($this->request->getPost('titulo') ?? '');
        $mensaje      = trim($this->request->getPost('mensaje') ?? '');
        $rolesDestino = $this->request->getPost('roles_destino');
        $imagen       = $this->request->getFile('imagen');

        $error = $this->validarDatosComunicado($titulo, $mensaje, $rolesDestino, $imagen);

        if ($error !== null) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', $error)
                ->withInput();
        }

        $datosActualizar = [
            'titulo'              => $titulo,
            'mensaje'             => $mensaje,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
            $nuevoNombre = $imagen->getRandomName();
            $imagen->move(ROOTPATH . 'public/assets/img/comunicados', $nuevoNombre);

            if (!empty($comunicado['imagen'])) {
                $rutaAnterior = ROOTPATH . 'public/assets/img/comunicados/' . $comunicado['imagen'];

                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }

            $datosActualizar['imagen'] = $nuevoNombre;
        }

        $modelo->update($idComunicado, $datosActualizar);
        $modelo->guardarDestinos($idComunicado, $rolesDestino);

        return redirect()->to(base_url('/comunicados'))
            ->with('success', 'Comunicado actualizado correctamente.');
    }

    public function activar($idComunicado = null)
    {
        return $this->cambiarEstadoComunicado($idComunicado, 1, 'activado');
    }

    public function desactivar($idComunicado = null)
    {
        return $this->cambiarEstadoComunicado($idComunicado, 0, 'desactivado');
    }

    private function cambiarEstadoComunicado($idComunicado, $estado, $texto)
    {
        if (!$this->puedeGestionarComunicados()) {
            return redirect()->to(base_url('/dashboard'))
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        if ($idComunicado === null || !is_numeric($idComunicado)) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', 'ID de comunicado no válido.');
        }

        $modelo = new ComunicadosModelo();
        $comunicado = $modelo->obtenerComunicadoPorId($idComunicado);

        if (!$comunicado) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', 'Comunicado no encontrado.');
        }

        if ((int)$comunicado['id_usuario'] !== (int)session()->get('id_usuario')) {
            return redirect()->to(base_url('/comunicados'))
                ->with('error', 'No tiene permisos para cambiar el estado de este comunicado.');
        }

        $modelo->cambiarEstado($idComunicado, $estado);

        return redirect()->to(base_url('/comunicados'))
            ->with('success', 'Comunicado ' . $texto . ' correctamente.');
    }

    public function misComunicados()
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        $modelo = new ComunicadosModelo();

        $buscar = trim($this->request->getGet('buscar') ?? '');

        if (mb_strlen($buscar) > 80) {
            $buscar = mb_substr($buscar, 0, 80);
        }

        if ($buscar !== '') {
            $comunicados = $modelo->buscarComunicadosPorRol(session()->get('id_rol'), $buscar);
        } else {
            $comunicados = $modelo->listarComunicadosPorRol(session()->get('id_rol'));
        }

        return view('Comunicados/mis_comunicados', [
            'comunicados' => $comunicados
        ]);
    }

    public function ver($idComunicado = null)
    {
        if (!session()->get('logueado')) {
            return redirect()->to(base_url('/login'));
        }

        if ($idComunicado === null || !is_numeric($idComunicado)) {
            return redirect()->to(base_url('/mis-comunicados'))
                ->with('error', 'Comunicado no válido.');
        }

        $modelo = new ComunicadosModelo();

        $comunicado = $modelo->obtenerComunicadoDetalle(
            $idComunicado,
            session()->get('id_rol')
        );

        if (!$comunicado) {
            return redirect()->to(base_url('/mis-comunicados'))
                ->with('error', 'No tiene acceso a este comunicado.');
        }

        return view('Comunicados/ver', [
            'comunicado' => $comunicado
        ]);
    }
}