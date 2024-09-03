@if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'admin'|| auth()->user()->role == 'coordinador' )

<li class="nav-item">
    <a href="{{ route('dashboard') }}" class="nav-link text-truncate @if (request()->routeIs('dashboard')) active @endif">
        <i class="bi-speedometer2 me-2"></i> {{ __('admin.dashboard') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('group_operator.index') }}" class="nav-link text-truncate @if (request()->routeIs('group_operator.*')) active @endif">
        <i class="bi-people-fill me-2"></i> {{ __('admin.group_operator_assignments') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.session_logs.index') }}" class="nav-link text-truncate @if (request()->routeIs('admin.session_logs.*')) active @endif">
        <i class="bi-clock-history me-2"></i> {{ __('admin.asistencia_registro') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('admin.gestion-breaks') }}" class="nav-link text-truncate @if (request()->routeIs('admin.gestion-breaks')) active @endif">
        <i class="bi-clock-history me-2"></i> {{ __('admin.registro_break') }}
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('work_plans.index') }}" class="nav-link text-truncate @if (request()->routeIs('work_plans.*')) active @endif">
        <i class="bi-calendar-week me-2"></i> {{ __('admin.work_plans') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('operative-reports.index') }}" class="nav-link text-truncate @if (request()->routeIs('operative-reports.*')) active @endif">
        <i class="bi-file-earmark-text me-2"></i> {{ __('admin.operative_reports') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('girls.index') }}" class="nav-link text-truncate @if (request()->routeIs('girls.*')) active @endif">
        <i class="bi-person-badge me-2"></i> {{ __('admin.girls') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('groups.index') }}" class="nav-link text-truncate @if (request()->routeIs('groups.*')) active @endif">
        <i class="bi-people me-2"></i> {{ __('admin.groups') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('groups.index') }}" class="nav-link text-truncate @if (request()->routeIs('categories.*')) active @endif">
        <i class="bi-people me-2"></i> {{ __('admin.categories') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link text-truncate @if (request()->routeIs('users.*')) active @endif">
        <i class="bi-person me-2"></i> {{ __('admin.users') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('points.index') }}" class="nav-link text-truncate @if (request()->routeIs('points.*')) active @endif">
        <i class="bi-trophy me-2"></i> {{ __('admin.points') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('audits.index') }}" class="nav-link text-truncate @if (request()->routeIs('audits.*')) active @endif">
        <i class="bi-clipboard-check me-2"></i> {{ __('admin.audit') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('digital.index') }}" class="nav-link text-truncate @if (request()->routeIs('digital.*')) active @endif">
        <i class="bi-file-earmark-richtext me-2"></i> {{ __('admin.digital_content') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('fooditems.index') }}" class="nav-link text-truncate @if (request()->routeIs('FoodItems.*')) active @endif">
        <i class="bi-egg-fried me-2"></i> {{ __('admin.FoodItems') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('platforms.index') }}" class="nav-link text-truncate @if (request()->routeIs('platforms.*')) active @endif">
        <i class="bi-display me-2"></i> {{ __('admin.platforms') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('reports.index') }}" class="nav-link text-truncate @if (request()->routeIs('reports.*')) active @endif">
        <i class="bi-file-earmark-bar-graph me-2"></i> {{ __('admin.reports') }}
    </a>
</li>
    @if (auth()->user()->role == 'super_admin')
        <li class="nav-item">
            <a href="{{ route('extension_chrome.index') }}" class="nav-link text-truncate @if (request()->routeIs('extension_chrome.*')) active @endif">
                <i class="bi-browser-chrome me-2"></i> {{ __('admin.extension_chrome') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('automatized_task.index') }}" class="nav-link text-truncate @if (request()->routeIs('automatized_task')) active @endif">
                <i class="bi-robot me-2"></i> {{ __('admin.automatized_task') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users') }}" class="nav-link text-truncate @if (request()->routeIs('admin.users')) active @endif">
                <i class="bi-person-gear me-2"></i> {{ __('admin.users_admins') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('permissions_and_roles.index') }}" class="nav-link text-truncate @if (request()->routeIs('permissions_and_roles.*')) active @endif">
                <i class="bi-shield-lock me-2"></i> {{ __('admin.permissions_and_roles') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('settings_jarvis.index') }}" class="nav-link text-truncate @if (request()->routeIs('settings_jarvis.*')) active @endif">
                <i class="bi-gear-wide-connected me-2"></i> {{ __('admin.settings_jarvis') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('SAfoodProducts.index') }}" class="nav-link text-truncate @if (request()->routeIs('SAfoodProducts.*')) active @endif">
                <i class="bi-basket me-2"></i> {{ __('admin.SAfoodProducts') }}
            </a>
        </li>
    @endif
@elseif (auth()->user()->role == 'operator')
    <li class="nav-item">
        <a href="{{ route('dashboard') }}"
            class="nav-link text-truncate @if (request()->routeIs('dashboard')) active @endif">
            <i class="bi-speedometer2 me-2"></i> {{ __('admin.dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="#automatedTaskSubmenu" data-bs-toggle="collapse"
            class="nav-link text-truncate @if (request()->routeIs('automated_task*')) active @endif">
            <i class="bi-robot me-2"></i> {{ __('operator.automated_task') }}
            <i class="bi-chevron-down ms-auto"></i>
        </a>
        <ul class="collapse show nav flex-column ms-1" id="automatedTaskSubmenu" data-bs-parent="#menu">
            @php
                $userGroups = auth()->user()->groups;
                $platformsWithGirls = collect();
                foreach ($userGroups as $group) {
                    $platformsWithGirls = $platformsWithGirls->merge(
                        $group
                            ->platforms()
                            ->whereHas('girls', function ($query) use ($group) {
                                $query->where('group_id', $group->id);
                            })
                            ->get(),
                    );
                }
                $platformsWithGirls = $platformsWithGirls->unique('id');
            @endphp

            @forelse($platformsWithGirls as $platform)
                <li class="nav-item">
                    <a href="{{ route('automated_task.platform', $platform->id) }}"
                        class="nav-link text-truncate @if (request()->is('automated_task/platform/' . $platform->id)) active @endif">
                        <i class="bi-circle me-2 small"></i> {{ $platform->name }}
                    </a>
                </li>
            @empty
                <li class="nav-item">
                    <span class="nav-link text-truncate text-muted">
                        <i class="bi-exclamation-circle me-2 small"></i> {{ __('operator.no_platforms_assigned') }}
                    </span>
                </li>
            @endforelse
        </ul>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_points') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_points')) active @endif">
            <i class="bi-trophy me-2"></i> {{ __('operator.my_points') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_logins') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_logins')) active @endif">
            <i class="bi-clock-history me-2"></i> {{ __('operator.my_logins') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my-operative-reports') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_operative_reports')) active @endif">
            <i class="bi-file-earmark-text me-2"></i> {{ __('operator.my_operative_reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_work_plan') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_work_plan')) active @endif">
            <i class="bi-egg-fried me-2"></i> {{ __('operator.my_foodItems') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_work_plan') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_work_plan')) active @endif">
            <i class="bi-calendar-check me-2"></i> {{ __('operator.my_work_plan') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('profile.edit') }}"
            class="nav-link text-truncate @if (request()->routeIs('profile.edit')) active @endif">
            <i class="bi-gear me-2"></i> {{ __('operator.settings') }}
        </a>
    </li>
@endif
@if (auth()->user()->role == 'coperative')

<li class="nav-item">
    <a href="{{ route('dashboard') }}" class="nav-link text-truncate @if (request()->routeIs('dashboard')) active @endif">
        <i class="bi-speedometer2 me-2"></i> {{ __('admin.dashboard') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('foodAdmin.index') }}" class="nav-link text-truncate @if (request()->routeIs('foodAdmin.index')) active @endif">
        <i class="bi-person-workspace me-2"></i> {{ __('operator.foodProducts') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('foodAdmin.sales') }}" class="nav-link text-truncate @if (request()->routeIs('foodAdmin.sales')) active @endif">
        <i class="bi-basket2 me-2"></i> {{ __('operator.Ventas') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('foodAdmin.salesReport') }}" class="nav-link text-truncate @if (request()->routeIs('ffoodAdmin.salesReport')) active @endif">
        <i class="bi-file-earmark-bar-graph me-2"></i> {{ __('operator.foodReports') }}
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('profile.edit') }}" class="nav-link text-truncate @if (request()->routeIs('profile.edit')) active @endif">
        <i class="bi-gear me-2"></i> {{ __('operator.settings') }}
    </a>
</li>
@endif
