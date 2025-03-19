<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>
    <x-nav-link :href="route('emargements.index')" :active="request()->routeIs('emargements.*')">
        {{ __('Émargements') }}
    </x-nav-link>
    <x-nav-link :href="route('emargements.statistiques')" :active="request()->routeIs('emargements.statistiques')">
        {{ __('Statistiques') }}
    </x-nav-link>
</div> 