<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Días hasta eliminar permanentemente por modelo
    |--------------------------------------------------------------------------
    |
    | Indica cuántos días después de un soft delete debe borrarse un registro
    | de forma permanente. La clave es el nombre del modelo completo.
    |
    */
    'days_to_keep_deleted' => [
        \App\Models\Task::class => 30,
        \App\Models\Project::class  => 60,
    ],
];
