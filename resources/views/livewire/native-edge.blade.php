<div>
    <native:top-bar
            title="{{\Native\Mobile\Facades\System::isAndroid() ? 'Tableau de bord' : $title ?? 'Tableau de bord'}}"
            show-navigation-icon="{{\Native\Mobile\Facades\System::isAndroid()}}"
    >
        <native:top-bar-action
                id="home"
                icon="home"
                label="Accueil"
                url="{{ route('access.dashboard') }}"
        />

        <native:top-bar-action
                id="web"
                icon="globe-alt"
                label="Site"
                url="https://cfls.be"
        />
    </native:top-bar>
    <native:side-nav
            :gestures_enabled="false">
        <native:side-nav-header
                title="LSFBGO"
                subtitle="LsfbGo App"
                :show-close-button="true"
                pinned
        />


        <native:side-nav-item active="{{ request()->routeIs('scanner') }}" id="scanner-demo" icon="qrcode" url="{{ route('scanner') }}" label="Scanner" badge="New!" badge-color="blue"/>

        <native:horizontal-divider />
        <native:side-nav-group heading="Ressources" :expanded="false">
            <native:side-nav-item id="visit-site" icon="globe-alt" url="https://cfls.be" label="Cfls.be"/>
            <native:side-nav-item active="{{ request()->routeIs('deconnect') }}" id="logout" icon="arrow-left-start-on-rectangle" url="{{ route('deconnect') }}" label="Déconnexion"/>
            <div class="px-2 mt-2">
                <form action="{{ route('access.logout') }}" method="POST" class="w-full">
                    @csrf
                    <flux:button
                            type="submit"
                            variant="primary"
                            icon="arrow-left-start-on-rectangle"
                            class="w-full cursor-pointer"
                    >
                        Déconnexion
                    </flux:button>
                </form>
            </div>
        </native:side-nav-group>
    </native:side-nav>

    @if(session('data.token'))

    <native:bottom-nav>
        <native:bottom-nav-item
                id="scanner"
                label="Scanner"
                url="{{ route('scanner') }}"
                icon="qrcode"
                :active="request()->routeIs('scanner')"
                news="true"
        />

    </native:bottom-nav>
    @endif

</div>
