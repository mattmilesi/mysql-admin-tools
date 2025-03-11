<style>
    a {
        text-decoration: none;
        color: teal;
    }

    * {
        font-family: monospace;
    }

    code, textarea {
        font-family: monospace;
    }

    #topbar {
        display: flex;
        flex-flow: row wrap;
        width: 100%;
    }

    #menu {
        flex: 1;
    }

    #menu > a.active {
        font-weight: bold;
    }

    #userinfo {
        flex: none;
    }

    #logout_form {
        display: inline;
    }
</style>

<div id="topbar">
    <div id="menu">
        <b>DB Manager</b>&nbsp;&nbsp;
        <a href="{{ route('percona.show') }}" class="{{ request()->routeIs('percona.show') || request()->routeIs('percona.showWithCommands') ? 'active' : '' }}">Percona</a>
        &nbsp;|&nbsp;
        <a href="{{ route('usage.show') }}" class="{{ request()->routeIs('usage.show') ? 'active' : '' }}">PK Usage</a>
        <!--&nbsp;|&nbsp;-->
        <!--<a href="{{ route('config.show') }}" class="{{ request()->routeIs('config.show') ? 'active' : '' }}">Config</a>-->

    </div>
    <div id="userinfo">
        Welcome <b>{{ Auth::user()->name }}</b>
        |
        <form id="logout_form" method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="javascript:{}" onclick="document.getElementById('logout_form').submit();">Logout</a>
        </form>
    </div>
</div>

<hr>
