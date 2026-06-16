<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Secciones</h1>

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

    <div style="margin-bottom: 20px;">
        <a class="btn btn-guardar" href="#modal-insertar-seccion">Nueva Sección</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sección</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($secciones)): ?>
                    <?php foreach ($secciones as $seccion): ?>
                        <tr>
                            <td><?= esc($seccion['id_seccion']) ?></td>
                            <td><?= esc($seccion['nombre_seccion']) ?></td>

                            <td>
                                <?php if ((int)$seccion['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($seccion['fecha_creacion']) ?></td>

                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $seccion['id_seccion'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$seccion['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/secciones/desactivar/' . $seccion['id_seccion']) ?>"
                                       onclick="return confirm('¿Desea desactivar esta sección?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/secciones/activar/' . $seccion['id_seccion']) ?>"
                                       onclick="return confirm('¿Desea activar esta sección?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="sin-registros">No existen secciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-seccion" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Sección</h2>

        <form action="<?= base_url('/secciones/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombre_seccion">Nombre de sección</label>
                <input type="text"
                       name="nombre_seccion"
                       id="nombre_seccion"
                       placeholder="Ej: A, B, C, D"
                       required
                       minlength="1"
                       maxlength="1"
                       pattern="[A-Za-z]"
                       title="Solo se permite una letra. Ejemplo: A, B, C o D"
                       value="<?= esc(old('nombre_seccion')) ?>">
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($secciones)): ?>
    <?php foreach ($secciones as $seccion): ?>
        <div id="modal-editar-<?= $seccion['id_seccion'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Sección</h2>

                <form action="<?= base_url('/secciones/actualizar/' . $seccion['id_seccion']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombre_seccion_<?= $seccion['id_seccion'] ?>">Nombre de sección</label>
                        <input type="text"
                               name="nombre_seccion"
                               id="nombre_seccion_<?= $seccion['id_seccion'] ?>"
                               value="<?= esc($seccion['nombre_seccion']) ?>"
                               placeholder="Ej: A, B, C, D"
                               required
                               minlength="1"
                               maxlength="1"
                               pattern="[A-Za-z]"
                               title="Solo se permite una letra. Ejemplo: A, B, C o D">
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$seccion['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>