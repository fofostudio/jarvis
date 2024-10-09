@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.points') }} ({{ $points->count() }})</span>
        @if (auth()->user()->role != 'admin')
            <a href="{{ route('points.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
                <i class="bi-plus-lg"></i> {{ __('admin.add_points') }}
            </a>
        @endif
    </h5>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                @if (session('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <style>
                    .calendar {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .calendar th,
                    .calendar td {
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #dee2e6;
                    }

                    .calendar th {
                        background-color: #f8f9fa;
                    }

                    .calendar .complete {
                        background-color: #c3e6cb;
                    }

                    .calendar .partial {
                        background-color: #fff3cd;
                    }

                    .calendar .none {
                        background-color: #f5c6cb;
                    }

                    .calendar .today {
                        font-weight: bold;
                        outline: 2px solid #007bff;
                    }

                    .calendar td {
                        cursor: pointer;
                    }

                    .calendar td:hover {
                        background-color: #e9ecef;
                    }

                    .shift-indicator {
                        display: inline-block;
                        width: 10px;
                        height: 10px;
                        margin: 1px;
                        border-radius: 50%;
                    }

                    .shift-morning {
                        background-color: #ffc107;
                    }

                    .shift-afternoon {
                        background-color: #17a2b8;
                    }

                    .shift-night {
                        background-color: #6610f2;
                    }
                </style>

                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-body p-lg-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="#" class="btn btn-sm btn-outline-secondary" id="prevMonth">&lt; Prev</a>
                            <h5 class="mb-0 text-center" id="currentMonthYear">{{ $date->format('F Y') }}</h5>
                            <a href="#" class="btn btn-sm btn-outline-secondary" id="nextMonth">Next &gt;</a>
                        </div>
                        <table class="calendar">
                            <thead>
                                <tr>
                                    <th>Lun</th>
                                    <th>Mar</th>
                                    <th>Mier</th>
                                    <th>Jue</th>
                                    <th>Vie</th>
                                    <th>Sab</th>
                                    <th>Dom</th>
                                </tr>
                            </thead>
                            <tbody id="calendarBody">
                                <!-- Calendar will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-4">
                        <h5 class="mb-3">Registros</h5>
                        <div class="table-responsive p-0 records-section">
                            @include('points.records', ['points' => $points])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar puntos -->
    <div class="modal fade" id="editPointModal" tabindex="-1" aria-labelledby="editPointModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPointModalLabel">{{ __('admin.edit_points') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- El formulario se cargará aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentDate = new Date('{{ $date->format('Y-m-d') }}');
        let calendarData = @json($calendarData);
        let serverToday = new Date('{{ now()->format('Y-m-d') }}');

        function updateCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);

            $('#currentMonthYear').text(currentDate.toLocaleString('default', {
                month: 'long',
                year: 'numeric'
            }));

            let calendarHtml = '';
            let dayOfWeek = firstDay.getDay() || 7; // Adjust Sunday to be 7 instead of 0

            calendarHtml += '<tr>';
            for (let i = 1; i < dayOfWeek; i++) {
                calendarHtml += '<td></td>';
            }

            for (let day = 1; day <= lastDay.getDate(); day++) {
                if (dayOfWeek === 8) {
                    calendarHtml += '</tr><tr>';
                    dayOfWeek = 1;
                }

                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayData = calendarData[dateString] || {
                    status: 'none',
                    shifts: []
                };
                const isToday = serverToday.toDateString() === new Date(dateString).toDateString();

                // Ensure shifts is always an array
                const shifts = Array.isArray(dayData.shifts) ? dayData.shifts : [];

                calendarHtml += `<td class="${dayData.status} ${isToday ? 'today' : ''}" data-date="${dateString}">
            ${day}<br>
            ${['morning', 'afternoon', 'night'].map(shift => 
                `<span class="shift-indicator shift-${shift} ${shifts.includes(shift) ? '' : 'd-none'}"></span>`
            ).join('')}
        </td>`;

                dayOfWeek++;
            }

            while (dayOfWeek <= 7) {
                calendarHtml += '<td></td>';
                dayOfWeek++;
            }
            calendarHtml += '</tr>';

            $('#calendarBody').html(calendarHtml);

            // Add click event to calendar cells
            $('#calendarBody td').click(function() {
                const clickedDate = $(this).data('date');
                if (clickedDate) {
                    filterRecords(clickedDate);
                }
            });
        }

        $('#prevMonth').click(function(e) {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadMonthData();
        });

        $('#nextMonth').click(function(e) {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadMonthData();
        });

        function loadMonthData() {
            $.ajax({
                url: '{{ route('points.index') }}',
                type: 'GET',
                data: {
                    date: currentDate.toISOString().split('T')[0]
                },
                success: function(response) {
                    calendarData = response.calendarData;
                    updateCalendar();
                    updateRecordsTable(response.records);
                    $('#currentMonthYear').text(response.currentMonth);
                },
                error: function(xhr) {
                    console.error('Error loading month data:', xhr.responseText);
                }
            });
        }

        function filterRecords(date) {
            $.ajax({
                url: '{{ route('points.index') }}',
                type: 'GET',
                data: {
                    date: date,
                    filter: true // Add this to differentiate between month load and day filter
                },
                success: function(response) {
                    updateRecordsTable(response.records);
                },
                error: function(xhr) {
                    console.error('Error loading records:', xhr.responseText);
                }
            });
        }

        function updateRecordsTable(recordsHtml) {
            $('.records-section').html(recordsHtml);

            // Destroy previous DataTable instance if it exists
            if ($.fn.DataTable.isDataTable('#recordsTable')) {
                $('#recordsTable').DataTable().destroy();
            }

            $('#recordsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "pageLength": 50,
                "lengthMenu": [
                    [50, 100, -1],
                    [50, 100, "Todos"]
                ],
                "responsive": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "searching": true,
                "drawCallback": function(settings) {
                    // You can add any function you need to execute after drawing the table
                    setupActionDelete();
                }
            });
        }

        function openEditModal(pointId) {
            $.ajax({
                url: `/points/${pointId}/edit`,
                type: 'GET',
                success: function(response) {
                    $('#editPointModal .modal-body').html(response);
                    $('#editPointModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Error loading edit form:', xhr.responseText);
                }
            });
        }

        $(document).on('submit', '#editPointForm', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#editPointModal').modal('hide');
                    loadMonthData(); // Reload the entire month data after editing
                    alert('Points updated successfully');
                },
                error: function(xhr) {
                    console.error('Error updating points:', xhr.responseText);
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    }
                }
            });
        });

        // Function to setup delete action
        function setupActionDelete() {
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }

        // Initialize calendar and table when the page loads
        $(document).ready(function() {
            updateCalendar();
            updateRecordsTable($('.records-section').html());
            setupActionDelete();
        });
    </script>
@endpush
