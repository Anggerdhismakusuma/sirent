@vite(['resources/css/app.scss', 'resources/js/app.js'])
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3 px-5">
    {{-- Logo Sirent --}}
    <a href="/">
        <img src="{{ asset("images/logo-sirent.png") }}" alt="" width="40" height="40">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-colapse" id="navbarContent">
        <form class="d-flex mx-auto col-12 col-lg-6 my-2 my-lg-0">
            <div class="input-group" style="border: 2px solid #022DC1; border-radius: 8px; overflow: hidden;">
                <input class="form-control border-0" type="search" placeholder="Find any items..." aria-label="Search">
                <button class="btn border-0" type="submit" style="background-color: #022DC1; color: white; border-radius: 0;">
                    <!-- Logo Kacamata Pembesar (Bootstrap Icons) -->
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</nav>