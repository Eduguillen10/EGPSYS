<?php

namespace App\Http\Controllers;

use App\Models\Bancos;
use App\Models\Cajas;
use App\Models\Cargos;
use App\Models\Chofer;
use App\Models\Ciudades;
use App\Models\Clientes;
use App\Models\Depositos;
use App\Models\Empleados;
use App\Models\Empresas;
use App\Models\EntidadEmisora;
use App\Models\FormaCobro;
use App\Models\Marcas;
use App\Models\Motivo;
use App\Models\Nacionalidades;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Rubros;
use App\Models\Sucursales;
use App\Models\Tarjeta;
use App\Models\Timbrado;
use App\Models\TipoArqueo;
use App\Models\TipoImpuesto;
use App\Models\TiposClientes;
use App\Models\TiposDocumentos;
use App\Models\Vehiculos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReferencialEstadoController extends Controller
{
    public function index(Request $request)
    {
        $referenciales = $this->referenciales();
        $recurso = $request->get('recurso', 'marcas');
        $estado = $request->get('estado', 'Inactivo');
        $searchText = trim((string) $request->get('searchText', ''));

        abort_unless(isset($referenciales[$recurso]), 404);

        $config = $referenciales[$recurso];
        $modelClass = $config['model'];
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass();

        $query = $modelClass::query();

        if ($estado !== 'Todos') {
            $query->where('estado', $estado);
        }

        if ($searchText !== '') {
            $query->where(function (Builder $query) use ($config, $searchText): void {
                foreach ($config['search'] as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $searchText . '%');
                }
            });
        }

        $registros = $query
            ->orderByDesc($model->getKeyName())
            ->paginate(12);

        return view('referenciales.estados.index', [
            'referenciales' => $referenciales,
            'recurso' => $recurso,
            'estado' => $estado,
            'searchText' => $searchText,
            'config' => $config,
            'primaryKey' => $model->getKeyName(),
            'registros' => $registros,
        ]);
    }

    public function reactivar(string $recurso, int $id): RedirectResponse
    {
        return $this->cambiarEstado($recurso, $id, 'Activo', 'Registro reactivado correctamente.');
    }

    public function inactivar(string $recurso, int $id): RedirectResponse
    {
        return $this->cambiarEstado($recurso, $id, 'Inactivo', 'Registro inactivado correctamente.');
    }

    private function cambiarEstado(string $recurso, int $id, string $estado, string $mensaje): RedirectResponse
    {
        $referenciales = $this->referenciales();

        abort_unless(isset($referenciales[$recurso]), 404);

        $modelClass = $referenciales[$recurso]['model'];
        $registro = $modelClass::findOrFail($id);
        $registro->forceFill(['estado' => $estado])->save();

        return redirect()
            ->route('referenciales.estados.index', [
                'recurso' => $recurso,
                'estado' => $estado === 'Activo' ? 'Activo' : 'Inactivo',
            ])
            ->with('success', $mensaje);
    }

    private function referenciales(): array
    {
        return [
            'marcas' => ['label' => 'Marcas', 'model' => Marcas::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'rubros' => ['label' => 'Rubros', 'model' => Rubros::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'bancos' => ['label' => 'Bancos', 'model' => Bancos::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'nacionalidades' => ['label' => 'Nacionalidades', 'model' => Nacionalidades::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'ciudades' => ['label' => 'Ciudades', 'model' => Ciudades::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'clientes' => ['label' => 'Clientes', 'model' => Clientes::class, 'search' => ['nombre', 'num_documento', 'email'], 'display' => ['nombre', 'num_documento', 'email']],
            'proveedores' => ['label' => 'Proveedores', 'model' => Proveedores::class, 'search' => ['razonsocial', 'ruc'], 'display' => ['razonsocial', 'ruc']],
            'tipo_impuesto' => ['label' => 'Tipo Impuesto', 'model' => TipoImpuesto::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'productos' => ['label' => 'Productos', 'model' => Productos::class, 'search' => ['codigo', 'descripcion'], 'display' => ['codigo', 'descripcion']],
            'cargos' => ['label' => 'Cargos', 'model' => Cargos::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'empleados' => ['label' => 'Empleados', 'model' => Empleados::class, 'search' => ['nombre', 'apellido', 'ci'], 'display' => ['nombre', 'apellido', 'ci']],
            'tipos_documentos' => ['label' => 'Tipo Documento', 'model' => TiposDocumentos::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'tipos_clientes' => ['label' => 'Tipo Cliente', 'model' => TiposClientes::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'depositos' => ['label' => 'Depositos', 'model' => Depositos::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'cajas' => ['label' => 'Cajas', 'model' => Cajas::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'tipo_arqueo' => ['label' => 'Tipo Arqueo', 'model' => TipoArqueo::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'vehiculos' => ['label' => 'Vehiculos', 'model' => Vehiculos::class, 'search' => ['nrochapa', 'color', 'chasis', 'modelo'], 'display' => ['nrochapa', 'color', 'chasis', 'modelo']],
            'choferes' => ['label' => 'Choferes', 'model' => Chofer::class, 'search' => ['nombre', 'apellido', 'ci', 'ruc'], 'display' => ['nombre', 'apellido', 'ci', 'ruc']],
            'formacobro' => ['label' => 'Forma Cobro', 'model' => FormaCobro::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'entidademisora' => ['label' => 'Entidad Emisora', 'model' => EntidadEmisora::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'tarjetas' => ['label' => 'Tarjetas', 'model' => Tarjeta::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'motivo' => ['label' => 'Motivos', 'model' => Motivo::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'empresas' => ['label' => 'Empresas', 'model' => Empresas::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'sucursales' => ['label' => 'Sucursales', 'model' => Sucursales::class, 'search' => ['descripcion'], 'display' => ['descripcion']],
            'timbrado' => ['label' => 'Timbrado', 'model' => Timbrado::class, 'search' => ['nro_timbrado', 'nro_serie'], 'display' => ['nro_timbrado', 'nro_serie']],
        ];
    }
}
