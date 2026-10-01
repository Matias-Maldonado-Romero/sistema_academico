  <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">

                        <li class="sidebar-item <?php echo (!isset($_GET['ruta']) || $_GET['ruta'] === 'inicio') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=inicio" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Inicio</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?php echo (isset($_GET['ruta']) && $_GET['ruta'] === 'materias') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=materias" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Materias</span>
                            </a>                           
                        </li>

                        <li class="sidebar-item <?php echo (isset($_GET['ruta']) && $_GET['ruta'] === 'carreras') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=carreras" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Carreras</span>
                            </a>
                          
                        </li>

                        <li class="sidebar-item <?php echo (isset($_GET['ruta']) && $_GET['ruta'] === 'calificaciones') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=calificaciones" class='sidebar-link'>
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Calificaciones</span>
                            </a>
                            
                        </li>

                    
                        <li class="sidebar-item <?php echo (isset($_GET['ruta']) && $_GET['ruta'] === 'inscripciones') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=inscripciones" class='sidebar-link'>
                                <i class="bi bi-file-earmark-medical-fill"></i>
                                <span>Inscripciones</span>
                            </a>
                        </li>

                        
                        <li class="sidebar-item <?php echo (isset($_GET['ruta']) && $_GET['ruta'] === 'grupos') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=grupos" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Grupos</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?php echo (isset($_GET['ruta']) && $_GET['ruta'] === 'usuarios') ? 'active' : ''; ?>">
                            <a href="index.php?ruta=usuarios" class='sidebar-link'>
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>

                       
                       

                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>