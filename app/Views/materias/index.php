<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Módulo de Materias</h1>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="mensaje-ok"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <a class="btn btn-guardar" href="#modalNuevaMateria" >Nueva Materia</a>


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
                        <td><?= esc($materia['descripcion']) ?></td>
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
                                <a href="<?= base_url('/materias/desactivar/' . $materia['id_materia']) ?>" class="btn btn-desactivar">Desactivar</a>
                            <?php else: ?>
                                <a href="<?= base_url('/materias/activar/' . $materia['id_materia']) ?>" class="btn btn-activar">Activar</a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <div id="modalEditarMateria<?= $materia['id_materia'] ?>" class="modal">
                        <div class="modal-contenido">
                            <a href="#" class="cerrar-modal">&times;</a>
                            <h2>Editar Materia</h2>

                            <form action="<?= base_url('/materias/actualizar/' . $materia['id_materia']) ?>" method="post">
                                <label>Nombre de materia</label>
                                <input type="text" name="nombre_materia" value="<?= esc($materia['nombre_materia']) ?>" required>

                                <label>Descripción</label>
                                <textarea name="descripcion"><?= esc($materia['descripcion']) ?></textarea>

                                <button type="submit" class="btn btn-guardar">Guardar cambios</button>
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
            <label>Nombre de materia</label>
            <input type="text" name="nombre_materia" required>

            <label>Descripción</label>
            <textarea name="descripcion"></textarea>

            <button type="submit" class="btn btn-guardar">Guardar</button>
        </form>
    </div>
</div>

<?= view('layout/footer') ?>