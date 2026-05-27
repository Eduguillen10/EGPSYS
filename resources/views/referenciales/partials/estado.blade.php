@if (($estado ?? 'Activo') === 'Activo')
    <span class="label label-success">Activo</span>
@else
    <span class="label label-danger">Inactivo</span>
@endif
