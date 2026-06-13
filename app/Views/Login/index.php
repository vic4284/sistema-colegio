<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Colegio</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body class="body-login">

<?php
    $esActivarCuenta = uri_string() === 'activar-cuenta';
    $esOlvidePassword = uri_string() === 'olvide-password';
    $paso = $_GET['paso'] ?? ($esOlvidePassword ? 'correo' : 'correo');
    $correo = $_GET['correo'] ?? '';
?>
<div class="contenedor-login">

    <div class="logo-login">
        <img src="<?= base_url('assets/img/logo/logo_Sea.png') ?>" alt="Logo SEA">
    </div>

    <h2>
    <?=
        $esActivarCuenta ? 'Activar Cuenta' :
        ($esOlvidePassword ? 'Recuperar Contraseña' : 'Iniciar Sesión')
    ?>
</h2>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="mensaje-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="mensaje-ok">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if($esActivarCuenta): ?>
        <?php if($paso === 'correo'): ?>
            <form action="<?= base_url('/activar-cuenta/enviar-codigo') ?>" method="post">
                <?= csrf_field() ?>
                <div class="grupo">
                    <label for="correo">Correo registrado</label>
                    <input type="email"
                           name="correo"
                           id="correo"
                           placeholder="Ingrese su correo institucional"
                           required>
                </div>
                <button type="submit" class="btn-login">Enviar código</button>
                <div class="acciones-login">
                    <a href="<?= base_url('/login') ?>" class="btn-secundario">Volver al login</a>
                </div>
            </form>
        <?php endif; ?>
        <?php if($paso === 'codigo'): ?>
            <form action="<?= base_url('/activar-cuenta/verificar-codigo') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="correo" value="<?= esc($correo) ?>">
                <div class="grupo">
                    <label>Correo</label>
                    <input type="text" value="<?= esc($correo) ?>" disabled>
                </div>
                <div class="grupo">
                    <label for="codigo">Código de verificación</label>
                    <input type="text"
                           name="codigo"
                           id="codigo"
                           placeholder="Ingrese el código de 6 dígitos"
                           required>
                </div>
                <button type="submit" class="btn-login">Verificar código</button>
            </form>
        <?php endif; ?>

        <?php if($paso === 'registro'): ?>
            <form action="<?= base_url('/activar-cuenta/registrar') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <input type="hidden" name="correo" value="<?= esc($correo) ?>">

        <div class="grupo">
            <label>Correo verificado</label>
            <input type="text" value="<?= esc($correo) ?>" disabled>
        </div>

        <div class="grupo">
            <label for="nombre_usuario">Nombre de usuario</label>
                <input type="text"
                name="nombre_usuario"
                id="nombre_usuario"
                placeholder="Ingrese su nombre de usuario"
                required>
        </div>

        <div class="grupo">
            <label for="contrasena">Contraseña</label>
            <input type="password"
            name="contrasena"
            id="contrasena"
            required>
        </div>

        <div class="grupo">
            <label for="confirmar_contrasena">Confirmar contraseña</label>
            <input type="password"
            name="confirmar_contrasena"
            id="confirmar_contrasena"
            required>
        </div>

        <div class="grupo">
            <label for="imagen">Foto de perfil (opcional)</label>
            <input type="file"
            name="imagen"
            id="imagen"
            accept="image/png, image/jpeg, image/jpg">
        </div>

        <button type="submit" class="btn-login">Activar cuenta</button>
       </form>
    <?php endif; ?>



        <?php elseif($esOlvidePassword): ?>

    <?php if($paso === 'correo'): ?>
        <form action="<?= base_url('/olvide-password/enviar-codigo') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="correo">Correo registrado</label>
                <input type="email"
                       name="correo"
                       id="correo"
                       placeholder="Ingrese su correo registrado"
                       required>
            </div>

            <button type="submit" class="btn-login">Enviar código</button>

            <div class="acciones-login">
    <a href="<?= base_url('/login') ?>" class="btn-secundario">Volver al login</a>
</div>
        </form>
    <?php endif; ?>

    <?php if($paso === 'codigo'): ?>
        <form action="<?= base_url('/olvide-password/verificar-codigo') ?>" method="post">
            <?= csrf_field() ?>

            <input type="hidden" name="correo" value="<?= esc($correo) ?>">

            <div class="grupo">
                <label>Correo</label>
                <input type="text" value="<?= esc($correo) ?>" disabled>
            </div>

            <div class="grupo">
                <label for="codigo">Código de verificación</label>
                <input type="text"
                       name="codigo"
                       id="codigo"
                       placeholder="Ingrese el código de 6 dígitos"
                       required>
            </div>

            <button type="submit" class="btn-login">Verificar código</button>
        </form>
    <?php endif; ?>

    <?php if($paso === 'nueva'): ?>
        <form action="<?= base_url('/olvide-password/actualizar') ?>" method="post">
            <?= csrf_field() ?>

            <input type="hidden" name="correo" value="<?= esc($correo) ?>">

            <div class="grupo">
                <label>Correo verificado</label>
                <input type="text" value="<?= esc($correo) ?>" disabled>
            </div>

            <div class="grupo">
                <label for="contrasena">Nueva contraseña</label>
                <input type="password"
                       name="contrasena"
                       id="contrasena"
                       placeholder="Ingrese su nueva contraseña"
                       required>
            </div>

            <div class="grupo">
                <label for="confirmar_contrasena">Confirmar nueva contraseña</label>
                <input type="password"
                       name="confirmar_contrasena"
                       id="confirmar_contrasena"
                       placeholder="Confirme su nueva contraseña"
                       required>
            </div>

            <button type="submit" class="btn-login">Actualizar contraseña</button>
        </form>
    <?php endif; ?>






    <?php else: ?>

        <form action="<?= base_url('/login/autenticar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="login">Usuario o Correo</label>
                <input type="text"
                       name="login"
                       id="login"
                       placeholder="Ingrese su usuario o correo"
                       required>
            </div>

            <div class="grupo">
                <label for="password">Contraseña</label>
                <input type="password"
                       name="password"
                       id="password"
                       placeholder="Ingrese su contraseña"
                       required>
            </div>

            <button type="submit" class="btn-login">Ingresaaaar</button>

            <div class="acciones-login">
                <a href="<?= base_url('/activar-cuenta') ?>" class="btn-secundario">Activar cuenta</a>
                <a href="<?= base_url('/olvide-password') ?>" class="link-password">Olvidé mi contraseña</a>
            </div>
            </div>
        </form>

    <?php endif; ?>

</div>

</body>
</html>