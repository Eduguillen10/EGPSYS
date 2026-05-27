@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verificacion en dos pasos') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="text-muted">
                        Ingrese el codigo de 6 digitos enviado a su correo. El codigo vence en 30 segundos.
                    </p>

                    <form method="POST" action="{{ route('two-factor.verify') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="code" class="col-md-4 col-form-label text-md-end">{{ __('Codigo') }}</label>

                            <div class="col-md-6">
                                <input id="code" type="text" inputmode="numeric" maxlength="6" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verificar') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-3">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-link p-0">
                                    {{ __('Enviar nuevo codigo') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
