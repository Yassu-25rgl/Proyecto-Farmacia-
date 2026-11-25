<aside id="sidebar" class="sidebar bg-dark text-white d-flex flex-column flex-shrink-0 p-3" style="width: 280px; min-height: 100vh;">
    <!-- LOGO O TÍTULO DEL PANEL -->
    <a href="{{ route('dashboard.index') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2">
        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2 text-white fw-bold" style="width: 35px; height: 35px;">
            <i class="bi bi-plus-lg"></i>
        </div>
        <div>
            <span class="fs-5 fw-bold d-block lh-1">SJB</span>
            <small class="text-white-50" style="font-size: 10px;">Sistema de Venta de Medicamentos</small>
        </div>
    </a>
    
    <hr class="text-secondary">

    <!-- MENÚ DE NAVEGACIÓN -->
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        
        <!-- SECCIÓN PRINCIPAL -->
        <li class="nav-item px-2 pt-2 pb-1">
            <span class="text-uppercase text-white-50 fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Principal</span>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard.index') }}" 
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is('/') ? 'active_item' : null }}"
               aria-current="{{ request()->is('/') ? 'page' : null }}">
                <i class="bi bi-house-door fs-6"></i>
                <span class="small fw-medium">Dashboard</span>
            </a>
        </li>
        @if (auth()->user()->hasRole('Administrador', 'Químico farmacéutico'))
        <li class="nav-item">
            <a href="{{ route('medicines.index') }}"
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is(['medicines*']) ? 'active_item' : null }}"
               aria-current="{{ request()->is(['medicines*']) ? 'page' : null }}">
                <i class="bi bi-box-seam fs-6"></i>
                <span class="small fw-medium">Medicamentos</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart fs-6"></i>
                <span class="small fw-medium">Reportes</span>
            </a>
        </li>
        @endif

        <!-- SECCIÓN GESTIÓN -->
        <li class="nav-item px-2 pt-3 pb-1">
            <span class="text-uppercase text-white-50 fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Gestión</span>
        </li>
        <li class="nav-item">
            <a href="{{ route('customers.index') }}" 
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is(['customers*']) ? 'active_item' : null }}"
               aria-current="{{ request()->is(['customers*']) ? 'page' : null }}">
                <i class="bi bi-people fs-6"></i>
                <span class="small fw-medium">Clientes</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('sales.index') }}" 
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is(['sales*']) ? 'active_item' : null }}"
               aria-current="{{ request()->is(['sales*']) ? 'page' : null }}">
                <i class="bi bi-cart3 fs-6"></i>
                <span class="small fw-medium">Ventas</span>
            </a>
        </li>

        @if (auth()->user()->hasRole('Administrador'))
        <!-- SECCIÓN CONFIGURACIONES -->
        <li class="nav-item px-2 pt-3 pb-1">
            <span class="text-uppercase text-white-50 fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Configuraciones</span>
        </li>
        <li class="nav-item">
            <a href="{{ route('roles.index') }}" 
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is(['roles*']) ? 'active_item' : null }}"
               aria-current="{{ request()->is(['roles*']) ? 'page' : null }}">
                <i class="bi bi-shield fs-6"></i>
                <span class="small fw-medium">Roles</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}" 
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is(['users*']) ? 'active_item' : null }}"
               aria-current="{{ request()->is(['users*']) ? 'page' : null }}">
                <i class="bi bi-person-gear fs-6"></i>
                <span class="small fw-medium">Usuarios</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('categories.index') }}" 
               class="nav-link sidebar-link text-white-50 rounded-4 px-3 py-2 d-flex align-items-center gap-2 {{ request()->is(['categories*']) ? 'active_item' : null }}"
               aria-current="{{ request()->is(['categories*']) ? 'page' : null }}">
                <i class="bi bi-grid fs-6"></i>
                <span class="small fw-medium">Categorías</span>
            </a>
        </li>
        @endif
    </ul>

    <hr class="text-secondary">

    <!-- PIE DEL SIDEBAR / USUARIO -->
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-2 py-1" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-2 text-white fw-bold small" style="width: 30px; height: 30px;">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="lh-sm">
                <strong class="d-block small">{{ auth()->user()->name }}</strong>
                <span class="text-white-50 d-block" style="font-size: 11px;">{{ auth()->user()->email }}</span>
                <span class="text-white-50" style="font-size: 10px;">{{ auth()->user()->role->name }}</span>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Cerrar sesión</a></li>
        </ul>
    </div>
</aside>