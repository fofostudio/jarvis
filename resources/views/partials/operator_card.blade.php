<!-- partials/operator_card.blade.php -->
<div class="col-md-2 mb-3">
    <div class="card shadow h-100 {{ $cardClass }} operator-card" id="card-{{ $operator['id_operador'] }}"
        data-operator-id="{{ $operator['id_operador'] }}">

        <div class="card-body">
            <h5 class="card-title">
                @php
                    $nameParts = explode(' ', $operator['name']);
                    $displayName =
                        isset($nameParts[0]) && isset($nameParts[2])
                            ? $nameParts[0] . ' ' . $nameParts[2]
                            : $operator['name'];
                @endphp
                {{ $displayName }}
            </h5>
            <h6 class="card-subtitle mb-2">
                {{ $operator['current_group'] ? $operator['current_group']['name'] : 'Sin grupo' }}
            </h6>
            <p class="card-text">
                <span>Hora llegada:
                    {{ $operator['session_start'] ? \Carbon\Carbon::parse($operator['session_start'])->format('h:i:s A') : 'N/A' }}</span>
                <br>
                @if ($operator['is_on_break'])
                    <span>Inicio break:
                        {{ \Carbon\Carbon::parse($operator['break_start'])->format('h:i:s A') }}</span>
                    <br>
                    <span class="countdown" data-start="{{ $operator['break_start'] }}">Tiempo restante: 30:00</span>
                    <br>
                @endif
                @if ($operator['break_overtime'] > 0)
                    <span class="text-danger overtime">Sobrepasó:
                        {{ gmdate('H:i:s', $operator['break_overtime']) }}</span>
                    <br>
                @endif
                @if (isset($operator['session_end']) && $operator['session_end'])
                    <span>Cierre sesión:
                        {{ \Carbon\Carbon::parse($operator['session_end'])->format('h:i:s A') }}</span>
                    <br>
                @endif
                <span>
                    Estado:
                    <strong class="status-text">
                        <span class="badge bg-dark">{{ $operator['status'] }}</span>
                    </strong>
                </span>
            </p>
            <button class="btn btn-{{ $operator['is_on_break'] ? 'dark' : 'dark' }} btn-sm toggle-break"
                data-user-id="{{ $operator['id_operador'] }}"
                {{ $operator['status'] == 'Inactivo' || (!$operator['is_on_break'] && $operator['break_taken']) ? 'disabled' : '' }}>
                {{ $operator['is_on_break'] ? 'Finalizar Break' : 'Iniciar Break' }}
            </button>
        </div>
    </div>
</div>
