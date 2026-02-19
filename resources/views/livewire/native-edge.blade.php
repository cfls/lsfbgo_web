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

    </native:top-bar>
    <native:side-nav
            :gestures_enabled="false">
        <native:side-nav-header
                title="LSFBGO"
                subtitle="LsfbGo App"
                :show-close-button="true"
                pinned
        />


        <native:side-nav-item active="{{ request()->routeIs('scanner') }}" id="scanner-demo" icon="qrcode" url="{{ route('scanner') }}" label="Scanner" />

        <native:horizontal-divider />

            <native:side-nav-item id="visit-site" icon="globe-alt" url="https://cfls.be" label="Cfls.be"/>
            <native:side-nav-item active="{{ request()->routeIs('numbers.practice') }}" id="logout" icon="dashboard" url="{{ route('numbers.practice') }}" label="Chiffres"/>
            <native:side-nav-item active="{{ request()->routeIs('alphabet.practice') }}" id="logout" icon="dashboard" url="{{ route('alphabet.practice') }}" label="Épeler"/>
            <native:side-nav-item active="{{ request()->routeIs('games') }}" id="logout" icon="dashboard" url="{{ route('games') }}" label="Jeux"/>
            <native:side-nav-item active="{{ request()->routeIs('games.dragdrop') }}" id="logout" icon="dashboard" url="{{ route('games.dragdrop') }}" label="Jeu de lettres"/>
            <native:side-nav-item active="{{ request()->routeIs('access.logout') }}" id="logout" icon="exit" url="{{ route('access.logout') }}" label="Déconnexion"/>




    </native:side-nav>


    @if (!request()->routeIs(['dictionary', 'syllabus.themes','profile.parameters','syllabus.play','alphabet.practice']))
    <native:bottom-nav>
        <native:bottom-nav-item
                id="scanner"
                label="Scanner"
                url="{{ route('scanner') }}"
                icon="qrcode"
                :active="request()->routeIs('scanner')"

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
                label="Profil"
                url="{{ route('profile.edit') }}"
                icon="user"
                />

    </native:bottom-nav>
    @endif
</div>
