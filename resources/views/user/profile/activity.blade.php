{{-- resources/views/user/profile/activity.blade.php --}}
@extends('user.layouts.customer')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('customer')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Account Activity</h5>
                        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                            <select class="form-select form-select-sm" id="activityFilter">
                                <option value="">All Activities</option>
                                <option value="profile">Profile Updates</option>
                                <option value="security">Security Changes</option>
                                <option value="orders">Order Activities</option>
                                <option value="addresses">Address Changes</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary" onclick="exportActivity()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2 p-md-3">
                    @if ($activities->count() > 0)
                        <div class="activity-timeline">
                            @foreach ($activities as $activity)
                                <div class="activity-item mb-3 mb-md-4">
                                    <div class="d-flex">
                                        <div class="activity-icon flex-shrink-0">
                                            <div class="icon-circle bg-{{ $activity->properties['color'] ?? 'primary' }}">
                                                <i class="fas fa-{{ $activity->properties['icon'] ?? 'user' }}"></i>
                                            </div>
                                        </div>
                                        <div class="activity-content flex-grow-1 ms-2 ms-md-3">
                                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
                                                <div class="flex-grow-1 w-100">
                                                    <h6 class="activity-title mb-1">{{ $activity->description }}</h6>
                                                    <div class="activity-meta text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <span class="d-block d-sm-inline">{{ $activity->created_at->diffForHumans() }}</span>
                                                        <span class="d-block d-sm-inline ms-sm-2">{{ $activity->created_at->format('M d, Y h:i A') }}</span>
                                                    </div>
                                                    @if ($activity->properties && count($activity->properties) > 0)
                                                        <div class="activity-details mt-2">
                                                            @foreach ($activity->properties as $key => $value)
                                                                @if (!in_array($key, ['icon', 'color']))
                                                                    <span class="badge bg-light text-dark me-1 mb-1 d-inline-block">
                                                                        <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                        @if (is_array($value))
                                                                            @if (empty($value))
                                                                                <em>None</em>
                                                                            @else
                                                                                {{ implode(', ', array_map('ucfirst', $value)) }}
                                                                            @endif
                                                                        @elseif(is_bool($value))
                                                                            {{ $value ? 'Yes' : 'No' }}
                                                                        @elseif(is_null($value))
                                                                            <em>Not set</em>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="activity-actions flex-shrink-0">
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                        onclick="toggleDetails({{ $activity->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="activity-extra collapse" id="details-{{ $activity->id }}">
                                                <div class="card card-body bg-light mt-2 p-2 p-md-3">
                                                    <div class="row g-2 g-md-3">
                                                        <div class="col-12 col-md-6">
                                                            <div class="mb-2">
                                                                <strong>Subject:</strong><br>
                                                                <span class="text-break">{{ $activity->subject_type ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="mb-2">
                                                                <strong>Subject ID:</strong><br>
                                                                <span class="text-break">{{ $activity->subject_id ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="mb-2">
                                                                <strong>Event:</strong><br>
                                                                <span class="text-break">{{ $activity->event ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <div class="mb-2">
                                                                <strong>IP Address:</strong><br>
                                                                <span class="text-break">{{ $activity->properties['ip'] ?? 'N/A' }}</span>
                                                            </div>
                                                            <div class="mb-2">
                                                                <strong>User Agent:</strong><br>
                                                                <span class="text-break small">{{ $activity->properties['user_agent'] ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if ($activity->properties && isset($activity->properties['changes']))
                                                        <div class="mt-2">
                                                            <strong>Changes:</strong>
                                                            <pre class="bg-white p-2 rounded text-break overflow-auto" style="font-size: 12px; max-height: 200px;">{{ json_encode($activity->properties['changes'], JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $activities->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
                            <h5>No activity recorded yet</h5>
                            <p class="text-muted">Your account activities will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Activity Summary</h6>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div class="row text-center g-2 g-md-3">
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <i class="fas fa-user-edit fa-2x text-primary mb-2"></i>
                                <h4 class="mb-1">{{ $activities->where('description', 'like', '%profile%')->count() }}</h4>
                                <small class="text-muted">Profile Updates</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <i class="fas fa-lock fa-2x text-success mb-2"></i>
                                <h4 class="mb-1">{{ $activities->where('description', 'like', '%password%')->count() }}</h4>
                                <small class="text-muted">Security Changes</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <i class="fas fa-map-marker-alt fa-2x text-info mb-2"></i>
                                <h4 class="mb-1">{{ $activities->where('description', 'like', '%address%')->count() }}</h4>
                                <small class="text-muted">Address Changes</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <i class="fas fa-shopping-cart fa-2x text-warning mb-2"></i>
                                <h4 class="mb-1">{{ $activities->where('description', 'like', '%order%')->count() }}</h4>
                                <small class="text-muted">Order Activities</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .activity-timeline {
            position: relative;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        @media (min-width: 768px) {
            .activity-timeline::before {
                left: 20px;
            }
        }

        .activity-item {
            position: relative;
            width: 100%;
        }

        .activity-icon {
            position: relative;
            z-index: 1;
        }

        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        @media (min-width: 768px) {
            .icon-circle {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
        }

        .activity-content {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            border-left: 3px solid #007bff;
            min-width: 0; /* Prevents flex items from overflowing */
        }

        @media (min-width: 768px) {
            .activity-content {
                padding: 15px;
            }
        }

        .activity-title {
            font-size: 14px;
            font-weight: 600;
            word-wrap: break-word;
            hyphens: auto;
        }

        .activity-meta {
            font-size: 11px;
        }

        @media (min-width: 576px) {
            .activity-meta {
                font-size: 12px;
            }
        }

        .activity-details .badge {
            font-size: 10px;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.4;
        }

        .stat-item {
            padding: 15px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        @media (min-width: 768px) {
            .stat-item {
                padding: 20px;
            }
        }

        .stat-item:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        .stat-item i {
            font-size: 1.5rem;
        }

        @media (min-width: 768px) {
            .stat-item i {
                font-size: 2rem;
            }
        }

        .stat-item h4 {
            font-size: 1.25rem;
        }

        @media (min-width: 768px) {
            .stat-item h4 {
                font-size: 1.5rem;
            }
        }

        .stat-item small {
            font-size: 0.75rem;
            display: block;
            margin-top: 5px;
        }

        /* Ensure text breaks properly on long content */
        .text-break {
            word-wrap: break-word !important;
            word-break: break-word !important;
            hyphens: auto;
        }

        /* Prevent horizontal overflow */
        .card-body {
            overflow-x: hidden;
        }

        /* Style improvements for mobile */
        @media (max-width: 575.98px) {
            .activity-content {
                margin-left: -5px;
            }

            .activity-timeline::before {
                left: 13px;
            }

            .card-header .d-flex > * {
                min-width: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleDetails(activityId) {
            const details = document.getElementById(`details-${activityId}`);
            const button = event.target.closest('button');
            const icon = button.querySelector('i');

            if (details.classList.contains('show')) {
                details.classList.remove('show');
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                details.classList.add('show');
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        function exportActivity() {
            // Implement export functionality
            alert('Export functionality will be implemented soon!');
        }

        // Filter activities
        document.getElementById('activityFilter').addEventListener('change', function() {
            const filter = this.value.toLowerCase();
            const activities = document.querySelectorAll('.activity-item');

            activities.forEach(activity => {
                const description = activity.querySelector('.activity-title').textContent.toLowerCase();

                if (filter === '' || description.includes(filter)) {
                    activity.style.display = 'block';
                } else {
                    activity.style.display = 'none';
                }
            });
        });
    </script>
@endpush
