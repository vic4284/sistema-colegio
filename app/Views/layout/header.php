<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Colegio</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>

<body>

<div class="header">

   <div class="logo-sistema">

    <img 
    src="<?= base_url('assets/img/logo/logo_Sea.png') ?>" 
    alt="Logo SEA"
    class="logo-imagen"
>

    <span class="texto-logo">SEA</span>

</div>

    <div class="menu">

        <a href="<?= base_url('/mis-comunicados') ?>" class="menu-link">Comunicados Publicados</a>
        <a href="<?= base_url('/comunicados') ?>" class="menu-link">Comunicados</a>

        <?php if(session('rol') === 'ADMINISTRATIVO'): ?>

            <a href="<?= base_url('/usuarios') ?>" class="menu-link">Usuarios</a>
            <a href="<?= base_url('/administrativos') ?>" class="menu-link">Administrativos</a>
            <a href="<?= base_url('/psicologos') ?>" class="menu-link">Psic&oacute;logos</a>
            <a href="<?= base_url('/profesores') ?>" class="menu-link">Profesores</a>
            <a href="<?= base_url('/estudiantes') ?>" class="menu-link">Estudiantes</a>

            <div class="dropdown">
                <button type="button" class="menu-dropdown">Acad&eacute;mico</button>

                <div class="dropdown-content">
                    <a href="<?= base_url('/materias') ?>">Materias</a>
                    <a href="<?= base_url('/secciones') ?>">Secciones</a>
                    <a href="<?= base_url('/paralelos') ?>">Paralelos</a>
                    <a href="<?= base_url('/aulas') ?>">Aulas</a>
                    <a href="<?= base_url('/horarios') ?>">Horarios</a>
                </div>
            </div>

            <div class="dropdown">
                <button type="button" class="menu-dropdown">Asignaciones</button>

                <div class="dropdown-content">
                    <a href="<?= base_url('/asignacion-profesores') ?>">Asig. Profesores</a>
                    <a href="<?= base_url('/asignacion-estudiantes') ?>">Asig. Estudiantes</a>
                </div>
            </div>

        <?php elseif(session('rol') === 'PSICOLOGIA'): ?>

            <a href="<?= base_url('/alertas') ?>" class="menu-link">Alertas</a>
         

        <?php elseif(session('rol') === 'PROFESOR'): ?>

            <a href="<?= base_url('/horarios-profesor') ?>" class="menu-link">Mis Horarios</a>
            <a href="<?= base_url('/notas-profesor') ?>" class="menu-link">Asignar Notas</a>

        <?php endif; ?>

    </div>

    <form method="GET" action="<?= current_url() ?>" class="buscador-header">

        <input 
            type="text" 
            name="buscar" 
            placeholder="Buscar en este m&oacute;dulo..."
            value="<?= esc($_GET['buscar'] ?? '') ?>"
            class="input-buscador-header"
        >

        <button type="submit" class="btn-buscar-header">Buscar</button>

        <?php if (!empty($_GET['buscar'])): ?>
            <a href="<?= current_url() ?>" class="btn-limpiar-header">X</a>
        <?php endif; ?>

    </form>

    <div class="perfil-header">

        <?php if(session('imagen')): ?>
            <img src="<?= base_url('assets/img/usuarios/' . session('imagen')) ?>" class="imagen-header">
        <?php else: ?>
            <img src="<?= base_url('assets/img/usuarios/default.png') ?>" class="imagen-header">
        <?php endif; ?>

        <div class="datos-usuario">
            <span><?= session('nombre_usuario') ?></span>
            <small><?= session('rol') ?></small>
        </div>

        <a href="<?= base_url('/logout') ?>" class="btn-logout">Salir</a>

    </div>

</div>

<div class="contenido">