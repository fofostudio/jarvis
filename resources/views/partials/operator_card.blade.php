<style>
    .operator-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0.5rem;
        overflow: hidden;
        position: relative;
        height: 100px; /* Altura fija para mantener forma rectangular */
        width: 100%;
    }

    .operator-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 0.5rem !important;
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .avatar-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: 150%; /* Zoom effect */
        background-position: center;
        opacity: 0.1;
        z-index: 1;
        transition: background-size 0.3s ease;
    }

    .operator-card:hover .avatar-background {
        background-size: 170%; /* More zoom on hover */
    }

    .avatar-circular {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 0.5rem;
        border: 1px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .avatar-circular img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .operator-info {
        display: flex;
        align-items: center;
    }

    .operator-details {
        flex-grow: 1;
    }

    .operator-name {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0;
        line-height: 1.1;
    }

    .operator-group {
        font-size: 0.7rem;
        opacity: 0.8;
    }

    .operator-status {
        font-size: 0.7rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.25rem;
    }

    .operator-actions {
        display: flex;
        gap: 0.25rem;
    }

    .operator-actions button {
        width: 24px;
        height: 24px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    }

    .operator-actions button:hover {
        transform: scale(1.1);
    }

    .toggle-break {
        background-color: #343a40;
        color: white;
        border: none;
    }

    .toggle-break:hover {
        background-color: #23272b;
    }

    .toggle-break:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }

    .close-session {
        background-color: transparent;
        color: #dc3545;
        border: 1px solid #dc3545;
    }

    .close-session:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<div class="col-md-2 col-sm-6 mb-2">
    <div class="card shadow-sm {{ $cardClass }} operator-card" id="card-{{ $operator['id_operador'] }}"
        data-operator-id="{{ $operator['id_operador'] }}">
        <div class="avatar-background" style="background-image: url('{{ $operator['avatar'] ? asset('storage/' . $operator['avatar']) : asset('images/default-avatar.png') }}');"></div>
        <div class="card-body p-2">
            <div class="operator-info">
                <div class="avatar-circular">
                    <img src="{{ $operator['avatar'] ? asset('storage/' . $operator['avatar']) : asset('images/default-avatar.png') }}" alt="{{ $operator['name'] }}">
                </div>
                <div class="operator-details">
                    <h6 class="card-title mb-0 operator-name">
                        @php
                            $nameParts = explode(' ', $operator['name']);
                            $displayName =
                                isset($nameParts[0]) && isset($nameParts[2])
                                    ? $nameParts[0] . ' ' . $nameParts[2]
                                    : $operator['name'];
                            $isActive = $cardClass === 'bg-success';
                        @endphp
                        {{ $displayName }}
                    </h6>
                    <div class="operator-group">
                        {{ $operator['current_group'] ? $operator['current_group']['name'] : 'Sin grupo' }}
                    </div>
                </div>
            </div>

            <div class="operator-status">
                <div>
                    @if ($operator['session_start'])
                        <i class="fas fa-clock fa-xs"></i>
                        {{ \Carbon\Carbon::parse($operator['session_start'])->format('h:i A') }}
                    @endif

                    @if ($operator['is_on_break'])
                        <i class="fas fa-mug-hot fa-xs ml-1"></i>
                        <span class="countdown" data-start="{{ $operator['break_start'] }}">30:00</span>
                    @endif

                    @if ($operator['break_overtime'] > 0)
                        <span class="text-danger overtime ml-1">
                            <i class="fas fa-exclamation-triangle fa-xs"></i>
                            +{{ gmdate('i:s', $operator['break_overtime']) }}
                        </span>
                    @endif
                </div>

                <div class="operator-actions">
                    <button class="toggle-break" data-user-id="{{ $operator['id_operador'] }}"
                        {{ $operator['status'] == 'Inactivo' || (!$operator['is_on_break'] && $operator['break_taken']) ? 'disabled' : '' }}
                        title="{{ $operator['is_on_break'] ? 'Fin Break' : 'Iniciar Break' }}">
                        <i class="fas {{ $operator['is_on_break'] ? 'fa-stop' : 'fa-mug-hot' }}"></i>
                    </button>

                    @if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'coordinador')
                        @if ($isActive)
                            <button class="close-session" data-user-id="{{ $operator['id_operador'] }}"
                                title="Cerrar Sesión">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
