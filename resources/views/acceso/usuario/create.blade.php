@extends ('layouts.admin')
@section ('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <h3>Nuevo Usuario</h3>
            @if (count($errors)>0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <form action="{{url('acceso/usuario')}}" method="POST" autocomplete="off"> 
    {{ csrf_field() }}
    <div class="row">
        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="form-group{{ $errors->has('name') ? ' text-danger' : '' }}">
                <label for="name" class="control-label">Nombre</label>
                <input id="name" type="text" class="form-control {{ $errors->has('name') ? '  is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>
         
        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="form-group{{ $errors->has('email') ? '  text-danger' : '' }}">
                <label for="email" class="control-label">E-Mail</label>
                <input id="email" type="email" class="form-control {{ $errors->has('email') ? '  is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
           
            </div>
        </div>

        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="form-group{{ $errors->has('password') ? '  text-danger' : '' }}">
                <label for="password" class="control-label">Contraseña</label>
                    <input id="password" type="password" class="form-control {{ $errors->has('password') ? '  is-invalid' : '' }}" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                
            </div>
        </div>

        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="form-group {{ $errors->has('password_confirmation') ? '  text-danger' : '' }}">
                <label for="password-confirm" class="control-label">Confirmar Contraseña</label>
                <input id="password-confirm" type="password" class="form-control {{ $errors->has('password_confirmation') ? '  is-invalid' : '' }}" name="password_confirmation" required>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>
        </div>

    </div>

    @include('acceso.usuario.permisos')

    <div class="row">
        <div class="col-lg-6 col-sm-6 col-xs-12">
            <div class="form-group">        
                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Guardar</button>
                <button class="btn btn-danger" type="reset" onclick="history.back(-1);"><i class="fa fa-arrow-left" aria-hidden="true"></i> Cancelar</button>
            </div>
        </div>

    </div>
    
    </form>

@endsection
