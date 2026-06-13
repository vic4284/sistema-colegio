<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Aulas</h1>

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
        <a class="btn btn-guardar" href="#modal-insertar-aula">Nueva Aula</a>
    </div>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aula</th>
                    <th>Capacidad</th>
                    <th>Estado</th>
                    <th>Fecha de creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($aulas)): ?>
                    <?php foreach ($aulas as $aula): ?>
                        <tr>
                            <td><?= esc($aula['id_aula']) ?></td>
                            <td><?= esc($aula['nombre_aula']) ?></td>
                            <td><?= esc($aula['capacidad']) ?></td>

                            <td>
                                <?php if ((int)$aula['estado'] === 1): ?>
                                    <span class="estado-activo">Activo</span>
                                <?php else: ?>
                                    <span class="estado-inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= esc($aula['fecha_creacion']) ?></td>

                            <td>
                                <a class="btn btn-editar" href="#modal-editar-<?= $aula['id_aula'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$aula['estado'] === 1): ?>
                                    <a class="btn btn-desactivar"
                                       href="<?= base_url('/aulas/desactivar/' . $aula['id_aula']) ?>"
                                       onclick="return confirm('¿Desea desactivar esta aula?')">
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-activar"
                                       href="<?= base_url('/aulas/activar/' . $aula['id_aula']) ?>"
                                       onclick="return confirm('¿Desea activar esta aula?')">
                                        Activar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="sin-registros">No existen aulas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-insertar-aula" class="modal">
    <div class="modal-contenido">
        <a href="#" class="modal-cerrar">&times;</a>

        <h2>Registrar Aula</h2>

        <form action="<?= base_url('/aulas/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombre_aula">Nombre del aula</label>
                <input type="text" name="nombre_aula" id="nombre_aula" placeholder="Ej: Aula 1" required>
            </div>

            <div class="grupo">
                <label for="capacidad">Capacidad</label>
                <input type="number" name="capacidad" id="capacidad" placeholder="Ej: 30" min="1" required>
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?php if (!empty($aulas)): ?>
    <?php foreach ($aulas as $aula): ?>
        <div id="modal-editar-<?= $aula['id_aula'] ?>" class="modal">
            <div class="modal-contenido">
                <a href="#" class="modal-cerrar">&times;</a>

                <h2>Editar Aula</h2>

                <form action="<?= base_url('/aulas/actualizar/' . $aula['id_aula']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="grupo">
                        <label for="nombre_aula_<?= $aula['id_aula'] ?>">Nombre del aula</label>
                        <input type="text"
                               name="nombre_aula"
                               id="nombre_aula_<?= $aula['id_aula'] ?>"
                               value="<?= esc($aula['nombre_aula']) ?>"
                               placeholder="Ej: Aula 1"
                               required>
                    </div>

                    <div class="grupo">
                        <label for="capacidad_<?= $aula['id_aula'] ?>">Capacidad</label>
                        <input type="number"
                               name="capacidad"
                               id="capacidad_<?= $aula['id_aula'] ?>"
                               value="<?= esc($aula['capacidad']) ?>"
                               min="1"
                               required>
                    </div>

                    <div class="grupo">
                        <label>Estado actual</label>
                        <input type="text" value="<?= (int)$aula['estado'] === 1 ? 'Activo' : 'Inactivo' ?>" disabled>
                    </div>

                    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                    <a href="#" class="btn btn-cancelar">Cancelar</a>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= view('layout/footer') ?>