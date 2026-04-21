@include('cabecera')

<div class="container mt-4">
    <h1 class="mb-4">Inicio</h1>

    <div class="row">
        @foreach($anuncios as $a)
            <div class="col-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $a->getTitulo }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $a->getPrecio }}</h6>
                        <p class="card-text mb-0">{{ $a->getDescripcion }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('pie')