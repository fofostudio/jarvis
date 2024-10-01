@if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'admin' || auth()->user()->role == 'coordinador')
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link text-truncate @if (request()->routeIs('dashboard')) active @endif">
            <i class="bi bi-speedometer2 me-2"></i> {{ __('admin.dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('group_operator.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('group_operator.*')) active @endif">
            <i class="bi bi-people-fill me-2"></i> {{ __('admin.group_operator_assignments') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.Messages')) active @endif">
            <i class="bi bi-chat-dots me-2"></i> {{ __('operator.MessagesChat') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.Messages')) active @endif">
            <i class="bi bi-envelope me-2"></i> {{ __('operator.CorporativeMail') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('schedule-calendar.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('schedule-calendar.index')) active @endif">
            <i class="bi bi-calendar-week me-2"></i> {{ __('admin.GestionHorarioTrabajo') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.session_logs.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('admin.session_logs.*')) active @endif">
            <i class="bi bi-clock-history me-2"></i> {{ __('admin.asistencia_registro') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.gestion-breaks') }}"
            class="nav-link text-truncate @if (request()->routeIs('admin.gestion-breaks')) active @endif">
            <i class="bi bi-clock-history me-2"></i> {{ __('admin.registro_break') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('work_plans.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('work_plans.*')) active @endif">
            <i class="bi bi-calendar-week me-2"></i> {{ __('admin.work_plans') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operative-reports.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('operative-reports.*')) active @endif">
            <i class="bi bi-file-earmark-text me-2"></i> {{ __('admin.operative_reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('girls.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('girls.*')) active @endif">
            <i class="bi bi-person-badge me-2"></i> {{ __('admin.girls') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('groups.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('groups.*')) active @endif">
            <i class="bi bi-people me-2"></i> {{ __('admin.groups') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('groups.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('categories.*')) active @endif">
            <i class="bi bi-trophy me-2"></i> {{ __('admin.ranking') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('group-categories.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('categories.*')) active @endif">
            <i class="bi bi-tags me-2"></i> {{ __('admin.categories') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('users.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('users.*')) active @endif">
            <i class="bi bi-person me-2"></i> {{ __('admin.users') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('points.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('points.*')) active @endif">
            <i class="bi bi-trophy me-2"></i> {{ __('admin.points') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('audits.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('audits.*')) active @endif">
            <i class="bi bi-clipboard-check me-2"></i> {{ __('admin.audit') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('team-lider.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('team-lider.*')) active @endif">
            <i class="bi bi-file-earmark-richtext me-2"></i> {{ __('admin.TeamSection') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('digital.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('digital.*')) active @endif">
            <i class="bi bi-file-earmark-richtext me-2"></i> {{ __('admin.digital_content') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.myShopItems')) active @endif">
            <i class="bi bi-egg-fried me-2"></i> {{ __('admin.my_foodItems') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('foodAdmin.paymentsGlobal') }}"
            class="nav-link text-truncate @if (request()->routeIs('foodAdmin.paymentsGlobal')) active @endif">
            <i class="bi bi-cash-coin me-2"></i> {{ __('admin.DeudasOperadoresCoperativa') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('platforms.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('platforms.*')) active @endif">
            <i class="bi bi-display me-2"></i> {{ __('admin.platforms') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('reports.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('reports.*')) active @endif">
            <i class="bi bi-file-earmark-bar-graph me-2"></i> {{ __('admin.reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('links.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('links.index')) active @endif">
            <i class="bi bi-link-45deg me-2"></i> {{ __('admin.linksManagement') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('category_links.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('category_links.index')) active @endif">
            <i class="bi bi-folder me-2"></i> {{ __('admin.CatManagement') }}
        </a>
    </li>

    @if (auth()->user()->role == 'super_admin')
        <li class="nav-item">
            <a href="{{ route('extension_chrome.index') }}"
                class="nav-link text-truncate @if (request()->routeIs('extension_chrome.*')) active @endif">
                <i class="bi bi-browser-chrome me-2"></i> {{ __('admin.extension_chrome') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('automatized_task.index') }}"
                class="nav-link text-truncate @if (request()->routeIs('automatized_task')) active @endif">
                <i class="bi bi-robot me-2"></i> {{ __('admin.automatized_task') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users') }}"
                class="nav-link text-truncate @if (request()->routeIs('admin.users')) active @endif">
                <i class="bi bi-person-gear me-2"></i> {{ __('admin.users_admins') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('permissions_and_roles.index') }}"
                class="nav-link text-truncate @if (request()->routeIs('permissions_and_roles.*')) active @endif">
                <i class="bi bi-shield-lock me-2"></i> {{ __('admin.permissions_and_roles') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('settings_jarvis.index') }}"
                class="nav-link text-truncate @if (request()->routeIs('settings_jarvis.*')) active @endif">
                <i class="bi bi-gear-wide-connected me-2"></i> {{ __('admin.settings_jarvis') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('SAfoodSales.index') }}"
                class="nav-link text-truncate @if (request()->routeIs('SAfoodSales.*')) active @endif">
                <i class="bi bi-basket me-2"></i> {{ __('admin.SAfoodSales') }}
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('SAfoodProducts.index') }}"
                class="nav-link text-truncate @if (request()->routeIs('SAfoodProducts.*')) active @endif">
                <i class="bi bi-box-seam me-2"></i> {{ __('admin.SAfoodProducts') }}
            </a>
        </li>
    @endif
@elseif (auth()->user()->role == 'operator')
    <li class="nav-item">
        <a href="{{ route('dashboard') }}"
            class="nav-link text-truncate @if (request()->routeIs('dashboard')) active @endif">
            <i class="bi bi-house-door me-2"></i> {{ __('admin.dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="#automatedTaskSubmenu" data-bs-toggle="collapse"
            class="nav-link text-truncate @if (request()->routeIs('automated_task*')) active @endif">
            <i class="bi bi-gear-fill me-2"></i> {{ __('operator.automated_task') }}
            <i class="bi bi-chevron-down ms-auto"></i>
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
                        <i class="bi bi-circle me-2 small"></i> {{ $platform->name }}
                    </a>
                </li>
            @empty
                <li class="nav-item">
                    <span class="nav-link text-truncate text-muted">
                        <i class="bi bi-exclamation-circle me-2 small"></i> {{ __('operator.no_platforms_assigned') }}
                    </span>
                </li>
            @endforelse
        </ul>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.Messages')) active @endif">
            <i class="bi bi-chat-dots me-2"></i> {{ __('operator.MessagesChat') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.Messages')) active @endif">
            <i class="bi bi-envelope me-2"></i> {{ __('operator.CorporativeMail') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_points') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_points')) active @endif">
            <i class="bi bi-star me-2"></i> {{ __('operator.my_points') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_logins') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_logins')) active @endif">
            <i class="bi bi-clock-history me-2"></i> {{ __('operator.my_logins') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_logins') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_audits')) active @endif">
            <i class="bi bi-clock-history me-2"></i> {{ __('operator.my_audits') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my-operative-reports') }}"
            class="nav-link text-truncate @if (request()->routeIs('my-operative-reports')) active @endif">
            <i class="bi bi-file-earmark-text me-2"></i> {{ __('operator.my_operative_reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('groups.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('ranking.*')) active @endif">
            <i class="bi bi-trophy me-2"></i> {{ __('admin.ranking') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('dictionary.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('dictionary.index')) active @endif">
            <i class="bi bi-link-45deg me-2"></i> {{ __('operator.interesLinks') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.myShopItems')) active @endif">
            <i class="bi bi-cart me-2"></i> {{ __('operator.my_foodItems') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('operator.myShopItems') }}"
            class="nav-link text-truncate @if (request()->routeIs('operator.searchReports')) active @endif">
            <i class="bi bi-search me-2"></i> {{ __('operator.searchReports') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_work_plan') }}"
            class="nav-link text-truncate @if (request()->routeIs('ChatWithJarvis')) active @endif">
            <i class="bi bi-robot me-2"></i> {{ __('operator.ChatWithJarvis') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('schedule-calendar.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('schedule-calendar.index')) active @endif">
            <i class="bi bi-calendar2-week me-2"></i> {{ __('operator.HorarioTrabajo') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_work_plan') }}"
            class="nav-link text-truncate @if (request()->routeIs('TempMail')) active @endif">
            <i class="bi bi-envelope-paper me-2"></i> {{ __('operator.TempMail') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_work_plan') }}"
            class="nav-link text-truncate @if (request()->routeIs('beneficiosMultas')) active @endif">
            <i class="bi bi-cash-coin me-2"></i> {{ __('operator.beneficiosMultas') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('my_work_plan') }}"
            class="nav-link text-truncate @if (request()->routeIs('my_work_plan')) active @endif">
            <i class="bi bi-calendar2-check me-2"></i> {{ __('operator.my_work_plan') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('profile.edit') }}"
            class="nav-link text-truncate @if (request()->routeIs('Support')) active @endif">
            <i class="bi bi-person-gear me-2"></i> {{ __('operator.Support') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('profile.edit') }}"
            class="nav-link text-truncate @if (request()->routeIs('profile.edit')) active @endif">
            <i class="bi bi-person-gear me-2"></i> {{ __('operator.settings') }}
        </a>
    </li>
@endif
@if (auth()->user()->role == 'coperative')
    <li class="nav-item">
        <a href="{{ route('foodAdmin.createSale') }}" class="nav-link btn-dark text-truncate">
            <i class="bi bi-cart-plus me-2"></i> {{ __('operator.registerSale') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('foodAdmin.sales') }}"
            class="nav-link text-truncate @if (request()->routeIs('foodAdmin.sales')) active @endif">
            <i class="bi bi-receipt me-2"></i> {{ __('operator.Ventas') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('foodAdmin.payments') }}"
            class="nav-link text-truncate @if (request()->routeIs('foodAdmin.payments')) active @endif">
            <i class="bi bi-cash-coin me-2"></i> {{ __('operator.abonos') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('dashboard') }}"
            class="nav-link text-truncate @if (request()->routeIs('dashboard')) active @endif">
            <i class="bi bi-speedometer2 me-2"></i> {{ __('admin.dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('foodAdmin.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('foodAdmin.index')) active @endif">
            <i class="bi bi-cup-hot me-2"></i> {{ __('operator.foodProducts') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('foodAdmin.categories.index') }}"
            class="nav-link text-truncate @if (request()->routeIs('foodAdmin.categories.index')) active @endif">
            <i class="bi bi-list-ul me-2"></i> {{ __('operator.foodCategories') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('foodAdmin.salesReport') }}"
            class="nav-link text-truncate @if (request()->routeIs('foodAdmin.salesReport')) active @endif">
            <i class="bi bi-graph-up me-2"></i> {{ __('operator.foodReports') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('profile.edit') }}"
            class="nav-link text-truncate @if (request()->routeIs('profile.edit')) active @endif">
            <i class="bi bi-person-gear me-2"></i> {{ __('operator.settings') }}
        </a>
    </li>
@endif
