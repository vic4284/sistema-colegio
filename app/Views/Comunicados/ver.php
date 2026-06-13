<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Detalle del Comunicado</h1>

    <div class="detalle-comunicado">
        <?php if (!empty($comunicado['imagen'])): ?>
            <div class="detalle-imagen">
                <img src="<?= base_url('assets/img/comunicados/' . $comunicado['imagen']) ?>" alt="Imagen del comunicado">
            </div>
        <?php endif; ?>

        <div class="detalle-info">
            <h2><?= esc($comunicado['titulo']) ?></h2>

            <p class="detalle-mensaje">
                <?= nl2br(esc($comunicado['mensaje'])) ?>
            </p>

            <div class="detalle-meta">
                <p><strong>Publicado por:</strong> <?= esc($comunicado['nombre_publicador']) ?></p>
                <p><strong>Rol:</strong> <?= esc($comunicado['nombre_rol']) ?></p>
                <p><strong>Fecha:</strong> <?= esc($comunicado['fecha_creacion']) ?></p>
            </div>

            <a href="<?= base_url('/mis-comunicados') ?>" class="btn btn-cancelar">Volver</a>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>