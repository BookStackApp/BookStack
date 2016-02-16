
<div class="faded-small toolbar">
    <div class="container">
        <div class="row">
            <div class="col-md-12 setting-nav">
                <a href="/settings" @if($selected == 'settings') class="selected text-button" @endif><i class="zmdi zmdi-settings"></i>Settings</a>
                <a href="/settings/users" @if($selected == 'users') class="selected text-button" @endif><i class="zmdi zmdi-accounts"></i>Users</a>
            </div>
        </div>
    </div>
</div>