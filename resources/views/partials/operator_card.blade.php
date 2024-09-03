<div class="col-md-2 col-sm-6 mb-2">
    <div class="card shadow-sm {{ $cardClass }} operator-card" id="card-{{ $operator['id_operador'] }}"
        data-operator-id="{{ $operator['id_operador'] }}">
        <div class="card-body p-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="card-title mb-0 ">
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

            </div>
            <strong class="strong text-dark mb-1">
                {{ $operator['current_group'] ? $operator['current_group']['name'] : 'Sin grupo' }}</strong>

            <div class="mb-1 small">
                @if ($operator['session_start'])
                    <i class="fas fa-clock fa-xs"></i>
                    {{ \Carbon\Carbon::parse($operator['session_start'])->format('h:i A') }}
                @endif

                @if ($operator['is_on_break'])
                    <br><i class="fas fa-mug-hot fa-xs"></i>
                    <span class="countdown" data-start="{{ $operator['break_start'] }}">30:00</span>
                @endif

                @if ($operator['break_overtime'] > 0)
                    <br><span class="text-danger overtime">
                        <i class="fas fa-exclamation-triangle fa-xs"></i>
                        +{{ gmdate('i:s', $operator['break_overtime']) }}
                    </span>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-dark btn-sm py-0 px-1 small toggle-break"
                    data-user-id="{{ $operator['id_operador'] }}"
                    {{ $operator['status'] == 'Inactivo' || (!$operator['is_on_break'] && $operator['break_taken']) ? 'disabled' : '' }}>
                    {{ $operator['is_on_break'] ? 'Fin Break' : 'Iniciar Break' }}
                </button>

                @if (auth()->user()->role == 'super_admin' || auth()->user()->role == 'coordinador' )
                    @if ($isActive)
                        <button class="btn btn-outline-danger btn-sm py-0 px-1 small close-session"
                            data-user-id="{{ $operator['id_operador'] }}">
                            <i class="fas fa-sign-out-alt fa-xs"></i>
                        </button>
                    @endif

                @endif

            </div>
        </div>
    </div>
</div>
