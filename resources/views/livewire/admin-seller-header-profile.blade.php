<div>
        @if (Auth::guard('admin')->check())
            <div class="user-info-dropdown">
                <div class="dropdown">
                    <a
                        class="dropdown-toggle"
                        href="#"
                        role="button"
                        data-toggle="dropdown"
                    >
                        <span class="user-icon">
                            <img src="{{ $admin->picture }}" alt="" />
                        </span>
                        <span class="user-name">{{ $admin->name }}</span>
                    </a>
                    <div
                        class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list"
                    >
                        <a class="dropdown-item" href="{{ route('admin.profile') }}"
                            ><i class="dw dw-user1"></i> Profile</a
                        >

                        <a class="dropdown-item" href="{{ route('admin.logout_handler') }}" onclick="event.preventDefault(); document.getElementById('adminLogoutForm').submit();"
                            ><i class="dw dw-logout"></i> Log Out</a
                        >
                        <form id="adminLogoutForm" action="{{ route('admin.logout_handler') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        @elseif( Auth::guard('seller')->check())
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a
                    class="dropdown-toggle"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                >
                    <span class="user-icon">
                        <img src="{{ $seller->picture }}" alt="" />
                    </span>
                    <span class="user-name">{{ $seller->name }}</span>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list"
                >
                    <a class="dropdown-item" href="{{ route('seller.profile') }}"
                        ><i class="dw dw-user1"></i> Profile</a
                    >
                    <a class="dropdown-item" href="{{ route('seller.logout') }}" onclick="event.preventDefault();document.getElementById('sellerLogoutForm').submit();"
                        ><i class="dw dw-logout"></i> Log Out</a
                    >
                    <form action="{{ route('seller.logout') }}" id="sellerLogoutForm" method="POST">@csrf</form>
                </div>
            </div>
        </div>
        @elseif( Auth::guard('client')->check())
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a
                    class="dropdown-toggle"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                >
                    <span class="user-icon">
                        <img src="{{ $client->picture }}" alt="" />
                    </span>
                    <span class="user-name">{{ $client->name }}</span>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list"
                >
                    <a class="dropdown-item" href="{{ route('client.profile') }}"
                        ><i class="dw dw-user1"></i> Profile</a
                    >
                   
                    <a class="dropdown-item" href="{{ route('client.logout') }}" onclick="event.preventDefault();document.getElementById('clientLogoutForm').submit();"
                        ><i class="dw dw-logout"></i> Log Out</a
                    >
                    <form action="{{ route('client.logout') }}" id="clientLogoutForm" method="POST">@csrf</form>
                </div>
            </div>
        </div>
        @endif
</div>
