<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
 <?= view('layout/header') ?>
<h1>Bienvenido al Dashboard</h1>

<p>Usuario: <?= esc($usuario) ?></p>
<p>Rol: <?= esc($rol) ?></p>

<a href="<?= base_url('/logout') ?>">Cerrar sesión</a>

</body>
</html>
<?= view('layout/footer') ?>