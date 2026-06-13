<?= view('layout/header') ?>

<div class="contenedor">
    <h1>Mis Horarios</h1>

    <div class="tabla-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Materia</th>
                    <th>Paralelo</th>
                    <th>Día</th>
                    <th>Hora</th>
                    <th>Aula</th>
                    <th>Gestión</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($horarios)): ?>
                    <?php foreach ($horarios as $horario): ?>
                        <tr>
                            <td><?= esc($horario['id_asignacion']) ?></td>
                            <td><?= esc($horario['nombre_materia']) ?></td>
                            <td><?= esc($horario['nombre_nivel'] . ' ' . $horario['nombre_grado'] . ' ' . $horario['nombre_seccion']) ?></td>
                            <td><?= esc($horario['dia']) ?></td>
                            <td><?= esc($horario['hora_inicio'] . ' - ' . $horario['hora_fin']) ?></td>
                            <td><?= esc($horario['nombre_aula'] . ' - Capacidad: ' . $horario['capacidad']) ?></td>
                            <td><?= esc($horario['nombre_gestion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="sin-registros">No tiene horarios asignados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr style="margin: 35px 0;">

    <h2>Horario Semanal</h2>

    <?php if (!empty($horarios)): ?>

        <?php
            $tablaHorario = [];

            foreach ($horarios as $h) {
                $hora = $h['hora_inicio'] . ' - ' . $h['hora_fin'];

                $tablaHorario[$hora][$h['dia']] =
                    '<strong>' . esc($h['nombre_materia']) . '</strong><br>' .
                    esc($h['nombre_nivel'] . ' ' . $h['nombre_grado'] . ' ' . $h['nombre_seccion']) . '<br>' .
                    esc($h['nombre_aula']);
            }
        ?>

        <div class="tabla-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Lunes</th>
                        <th>Martes</th>
                        <th>Miércoles</th>
                        <th>Jueves</th>
                        <th>Viernes</th>
                        <th>Sábado</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tablaHorario as $hora => $dias): ?>
                        <tr>
                            <td><?= esc($hora) ?></td>
                            <td><?= $dias['Lunes'] ?? '' ?></td>
                            <td><?= $dias['Martes'] ?? '' ?></td>
                            <td><?= $dias['Miércoles'] ?? '' ?></td>
                            <td><?= $dias['Jueves'] ?? '' ?></td>
                            <td><?= $dias['Viernes'] ?? '' ?></td>
                            <td><?= $dias['Sábado'] ?? '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="mensaje-error">
            No tiene horario semanal registrado.
        </div>
    <?php endif; ?>
</div>

<?= view('layout/footer') ?>