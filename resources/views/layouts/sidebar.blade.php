<div class="side-overlay"></div>

<aside class="sidebar">
    <!-- sidebar close btn -->
    <button type="button"
        class="sidebar-close-btn text-gray-500 hover-text-white hover-bg-main-600 text-md w-24 h-24 border border-gray-100 hover-border-main-600 d-xl-none d-flex flex-center rounded-circle position-absolute"><i
            class="ph ph-x"></i></button>
    <!-- sidebar close btn -->

    <a href="index.htm
        class="sidebar__logo text-center p-20 position-sticky inset-block-start-0 bg-white w-100 z-1 pb-10">
        <img src="{{asset('assets/images/bg/b-learninglogo.webp')}}" alt="Logo">
    </a>

    <div class="sidebar-menu-wrapper overflow-y-auto scroll-sm">
        <div class="p-20 pt-10">
            <ul class="sidebar-menu">

                <li class="sidebar-divider">
                    <span>Dashboard</span>
                </li>

                <li class="sidebar-menu__item {{ setActive('dashboard.*') }}">
                    <a href="{{ route('dashboard.index')}}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-squares-four"></i></span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-divider">
                    <span>Users</span>
                </li>

                <li class="sidebar-menu__item {{ setActive(['users*']) }}"">
                    <a href="{{ route('users')}}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-users-three"></i></span>
                        <span class="text">Users</span>
                    </a>
                </li>

                <li class="sidebar-divider">
                    <span>Rewards</span>
                </li>

                <li class="sidebar-menu__item {{ setActive(['rewards*']) }}">
                    <a href="{{ route('rewards')}}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-treasure-chest"></i></span>
                        <span class="text">Rewards</span>
                    </a>
                </li>


                <li class="sidebar-divider">
                    <span>Settings</span>
                </li>

                <li class="sidebar-menu__item {{ setActive(['category*']) }}">
                    <a href="{{ route('category')}}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-list-bullets"></i></span>
                        <span class="text">Kategori</span>
                    </a>
                </li>

                <li class="sidebar-menu__item {{ setActive(['coursetype*']) }}">
                    <a href="{{ route('coursetype')}}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-list-magnifying-glass"></i></span>
                        <span class="text">Tipe Kursus</span>
                    </a>
                </li>

                <li class="sidebar-menu__item {{ setActive(['location*']) }}">
                    <a href="{{ route('location')}}" class="sidebar-menu__link">
                        <span class="icon"><i class="ph ph-map-pin-line"></i></span>
                        <span class="text">Lokasi</span>
                    </a>
                </li>

            </ul>
        </div>

    </div>

</aside>
