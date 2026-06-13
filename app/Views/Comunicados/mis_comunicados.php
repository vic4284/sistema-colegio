<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Mis Comunicados</h1>

    <div class="contenedor-tarjetas-comunicados">
        <?php if (!empty($comunicados)): ?>
           <?php foreach ($comunicados as $comunicado): ?>
    <div class="tarjeta-comunicado">
        <?php if (!empty($comunicado['imagen'])): ?>
            <img src="<?= base_url('assets/img/comunicados/' . $comunicado['imagen']) ?>" class="imagen-tarjeta-comunicado">
        <?php endif; ?>

        <h3><?= esc($comunicado['titulo']) ?></h3>
        <p><?= esc(mb_strimwidth($comunicado['mensaje'], 0, 90, '...')) ?></p>

        <small><strong>Publicado por:</strong> <?= esc($comunicado['nombre_publicador']) ?></small><br>
        <small><strong>Rol:</strong> <?= esc($comunicado['nombre_rol']) ?></small><br>
        <small><strong>Fecha:</strong> <?= esc($comunicado['fecha_creacion']) ?></small>

        <div class="acciones-comunicado">
            <a href="<?= base_url('/comunicados/ver/' . $comunicado['id_comunicado']) ?>" class="btn btn-guardar">
                Ver publicación
            </a>
        </div>
    </div>
<?php endforeach; ?>
        <?php else: ?>
            <p>No existen comunicados para su rol.</p>
        <?php endif; ?>
    </div>
</div>

<?= view('layout/footer') ?>