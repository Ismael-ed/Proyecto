<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>WallaBook</title>
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('index') }}">WallaBook</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse w-100" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Mis anuncios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Mis compras</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Buzón</a></li>
                    @if ($user && $user->perfil == 'A')
                        <li class="nav-item"><a class="nav-link" href="#">Usuarios</a></li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-auto d-flex align-items-center">
                    <li class="nav-item me-3">
                        <span class="navbar-text">{{ $user->nombre ?? '' }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('cerrar') }}" class="d-inline">
                            @csrf
                            <button type="submit" name="cerrar" class="btn btn-outline-light btn-sm">
                                Cerrar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

