<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $stats['usuarios']; ?></h3>
                <p>Usuarios Registrados</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $stats['productos']; ?></h3>
                <p>Productos Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $stats['ventas']; ?></h3>
                <p>Ventas Realizadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>65%</h3>
                <p>Tasa de Conversión</p>
            </div>
            <div class="icon">
                <i class="fas fa-percent"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Actividad Reciente</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div>
                        <i class="fas fa-user bg-info"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> Hace 5 mins</span>
                            <h3 class="timeline-header">Nuevo usuario registrado</h3>
                        </div>
                    </div>
                    
                    <div>
                        <i class="fas fa-shopping-cart bg-success"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> Hace 12 mins</span>
                            <h3 class="timeline-header">Nueva venta completada</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información del Sistema</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-cog"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Versión de PHP</span>
                        <span class="info-box-number"><?php echo phpversion(); ?></span>
                    </div>
                </div>
                
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-memory"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Memoria Disponible</span>
                        <span class="info-box-number"><?php echo ini_get('memory_limit'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>