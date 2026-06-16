<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Materias</h1>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="mensaje-error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="mensaje-ok"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <a class="btn btn-guardar" href="#modalNuevaMateria">Nueva Materia</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Materia</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha creación</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if(!empty($materias)): ?>
                <?php foreach($materias as $materia): ?>
                    <tr>
                        <td><?= esc($materia['id_materia']) ?></td>
                        <td><?= esc($materia['nombre_materia']) ?></td>
                        <td><?= !empty($materia['descripcion']) ? esc($materia['descripcion']) : 'Sin descripción' ?></td>
                        <td>
                            <?php if($materia['estado'] == 1): ?>
                                <span class="estado-activo">Activo</span>
                            <?php else: ?>
                                <span class="estado-inactivo">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($materia['fecha_creacion']) ?></td>
                        <td>
                            <a href="#modalEditarMateria<?= $materia['id_materia'] ?>" class="btn btn-editar">Editar</a>

                            <?php if($materia['estado'] == 1): ?>
                                <a href="<?= base_url('/materias/desactivar/' . $materia['id_materia']) ?>"
                                   class="btn btn-desactivar"
                                   onclick="return confirm('¿Desea desactivar esta materia?')">
                                    Desactivar
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('/materias/activar/' . $materia['id_materia']) ?>"
                                   class="btn btn-activar"
                                   onclick="return confirm('¿Desea activar esta materia?')">
                                    Activar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <div id="modalEditarMateria<?= $materia['id_materia'] ?>" class="modal">
                        <div class="modal-contenido">
                            <a href="#" class="cerrar-modal">&times;</a>
                            <h2>Editar Materia</h2>

                            <form action="<?= base_url('/materias/actualizar/' . $materia['id_materia']) ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="grupo">
                                    <label for="nombre_materia_<?= $materia['id_materia'] ?>">Nombre de materia</label>
                                    <input type="text"
                                           name="nombre_materia"
                                           id="nombre_materia_<?= $materia['id_materia'] ?>"
                                           value="<?= esc($materia['nombre_materia']) ?>"
                                           placeholder="Ingrese el nombre de la materia"
                                           required
                                           minlength="3"
                                           maxlength="80"
                                           pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                                           title="Solo se permiten letras y espacios">
                                </div>

                                <div class="grupo">
                                    <label for="descripcion_<?= $materia['id_materia'] ?>">Descripción (opcional)</label>
                                    <textarea name="descripcion"
                                              id="descripcion_<?= $materia['id_materia'] ?>"
                                              placeholder="Ingrese una descripción opcional"
                                              maxlength="200"><?= esc($materia['descripcion']) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-guardar">Guardar cambios</button>
                                <a href="#" class="btn btn-cancelar">Cancelar</a>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No existen materias registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalNuevaMateria" class="modal">
    <div class="modal-contenido">
        <a href="#" class="cerrar-modal">&times;</a>
        <h2>Nueva Materia</h2>

        <form action="<?= base_url('/materias/insertar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="grupo">
                <label for="nombre_materia">Nombre de materia</label>
                <input type="text"
                       name="nombre_materia"
                       id="nombre_materia"
                       placeholder="Ingrese el nombre de la materia"
                       required
                       minlength="3"
                       maxlength="80"
                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                       title="Solo se permiten letras y espacios"
                       value="<?= esc(old('nombre_materia')) ?>">
            </div>

            <div class="grupo">
                <label for="descripcion">Descripción (opcional)</label>
                <textarea name="descripcion"
                          id="descripcion"
                          placeholder="Ingrese una descripción opcional"
                          maxlength="200"><?= esc(old('descripcion')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-guardar">Guardar</button>
            <a href="#" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</div>

<?= view('layout/footer') ?>