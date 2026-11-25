<header class="navbar navbar-expand bg-white border-bottom px-4 py-3">
    <div class="container-fluid p-0">
        <!-- Botón para colapsar sidebar o título de sección -->
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light border-0" type="button" id="sidebar-toggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h4 class="mb-0 fw-bold text-dark">@yield('name_section')</h4>
        </div>

        <!-- Notificaciones y perfil derecho -->
        <ul class="navbar-nav align-items-center gap-3">
            <li class="nav-item">
                <a href="#" class="text-dark position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </a>
            </li>
        </ul>
    </div>
</header>