<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Login - WallaBook</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0 text-center">Bienvenido a Mi intento de taller</h2>
                        <p>(No es obligatorio iniciar sesion esto es una prueba)</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            
                            <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" 
                                type="email" name="email" id="email" 
                                value="{{ old('email', 'ejemplo@gmail.com') }}" 
                                placeholder="ejemplo@gmail.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ps" class="form-label">Contraseña</label>
                            <input class="form-control @error('ps') is-invalid @enderror" 
                                type="password" name="ps" id="ps" 
                                value="{{ old('ps', '123') }}">
                            @error('ps')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            
                            @if(session('mensaje'))
                                <div class="alert alert-success">{{ session('mensaje') }}</div>
                            @endif

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <button type="submit" name="iniciar" class="btn btn-primary btn-lg">Iniciar</button>
                                <button type="reset" class="btn btn-secondary btn-lg">Cancelar</button>
   
                                <a href="#" class="btn btn-success btn-lg">Registrarse (No va,aún)</a>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>