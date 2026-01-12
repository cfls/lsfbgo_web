<div>


    <native:top-bar
            title="{{\Native\Mobile\Facades\System::isAndroid() ? $title : 'Native Edge'  }}"
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
            <native:side-nav-item active="{{ request()->routeIs('access.logout') }}" id="logout" icon="arrow-left-start-on-rectangle" url="{{ route('access.logout') }}" label="Déconnexion"/>



        </native:side-nav-group>
    </native:side-nav>



    <native:bottom-nav>
        <native:bottom-nav-item
                id="scanner"
                label="Scanner"
                url="{{ route('scanner') }}"
                icon="qrcode"
                :active="request()->routeIs('scanner')"
                news="true"
        />
        <native:bottom-nav-item
                id="syllabus"
                label="Syllabus"
                url="{{ route('syllabus') }}"
                icon="book-open"
                :active="request()->routeIs('syllabus')"

        />
        <native:bottom-nav-item
                id="jeux"
                label="Jeux"
                url="{{ route('games') }}"
                icon="computer-desktop"
                :active="request()->routeIs('games')"

        />
        <native:bottom-nav-item
                :active="request()->routeIs('profile')"
                id="profile"
                label="Profila"
                url="{{ route('profile.edit') }}"
                icon="user"
                />

    </native:bottom-nav>

</div>
