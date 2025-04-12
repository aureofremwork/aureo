<?php
$title = 'Página de inicio';
require_once __DIR__ . '/../layouts/adminlte.php';
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Bienvenido a Áureo Framework</h1>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
                
                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>Moderno</h3>
                                <p>Diseño responsive y actual</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-desktop"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Flexible</h3>
                                <p>Arquitectura adaptable</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-code"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>Seguro</h3>
                                <p>Implementación robusta</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                    </div>
                </div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Sistema de Plantillas</h5>
            </div>
            <div class="card-body">
                <h6 class="card-title">Características principales:</h6>
                <ul>
                    <li>Layouts base extendibles</li>
                    <li>Secciones dinámicas con @section y @yield</li>
                    <li>Sintaxis similar a Blade</li>
                    <li>Fácil de usar y mantener</li>
                </ul>
                <a href="https://github.com/tu-usuario/aureo" class="btn btn-primary">Ver documentación</a>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Scripts -->
<script>
    console.log('Vista de inicio cargada correctamente');
</script>