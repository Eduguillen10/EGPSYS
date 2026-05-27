@if (isset($modulos, $acciones))
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h4>Permisos por modulo, ventana y escenario</h4>
            <div class="table-responsive">
                @foreach ($modulos as $modulo)
                    <h5>{{ $modulo->nombre }}</h5>
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Ventana</th>
                                @foreach ($acciones as $accion)
                                    <th class="text-center">{{ $accion->nombre }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modulo->ventanas as $ventana)
                                @php
                                    $seleccionadas = old('permisos.' . $ventana->idventana, $permisosAsignados[$ventana->idventana] ?? []);
                                    $seleccionadas = array_map('intval', (array) $seleccionadas);
                                @endphp
                                <tr>
                                    <td>{{ $ventana->nombre }}</td>
                                    @foreach ($acciones as $accion)
                                        <td class="text-center">
                                            <input
                                                type="checkbox"
                                                name="permisos[{{ $ventana->idventana }}][]"
                                                value="{{ $accion->idaccion }}"
                                                {{ in_array((int) $accion->idaccion, $seleccionadas, true) ? 'checked' : '' }}>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </div>
@endif
