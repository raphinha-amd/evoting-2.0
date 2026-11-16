<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fa-regular fa-fw fa-envelope-open-text"></i>
        </div>
        <div class="sidebar-brand-text mx-3">RuangSuara <sup>KPUM BSI</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ $menu_type == 'dashboard' ? 'active' : '' }}">
        @if (auth()->user()->role == 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-house"></i>
                <span>Dasbor</span>
            </a>
        @else
            <a href="{{ route('user.dashboard') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-house"></i>
                <span>Dasbor</span>
            </a>
        @endif
    </li>

    @if (auth()->user()->role == 'admin')
    <hr class="sidebar-divider">
        <div class="sidebar-heading">Kelola Kampus</div>

        <li class="nav-item {{ $menu_type == 'manage-prodi' ? 'active' : '' }}">
            <a href="{{ route('admin.manage.prodi') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-graduation-cap"></i>
                <span>Kelola Prodi</span>
            </a>
        </li>
        <li class="nav-item {{ $menu_type == 'manage-class' ? 'active' : '' }}">
            <a href="{{ route('admin.manage.class') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-chalkboard-user"></i>
                <span>Kelola Kelas</span>
            </a>
        </li>
        <li class="nav-item {{ $menu_type == 'manage-ukm' ? 'active' : '' }}">
            <a href="{{ route('admin.manage.ukm') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-people-group"></i>
                <span>Kelola UKM</span>
            </a>
        </li>
        <li class="nav-item {{ $menu_type == 'manage-roles' ? 'active' : '' }}">
            <a href="{{ route('admin.manage.roles') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-id-badge"></i>
                <span>Kelola Role</span>
            </a>
        </li>
        <li class="nav-item {{ $menu_type == 'manage-user' ? 'active' : '' }}">
            <a href="{{ route('admin.manage.users') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-users"></i>
                <span>Kelola User</span>
            </a>
        </li>

    <hr class="sidebar-divider">
        <div class="sidebar-heading">Kelola Pemilu (KPUM)</div>

        <li class="nav-item {{ $menu_type == 'manage-pemilu' ? 'active' : '' }}">
            <a href="{{ route('admin.manage.pemilu') }}" class="nav-link">
                <i class="fa-regular fa-fw fa-check-to-slot"></i>
                <span>Kelola Pemilu</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
