@extends('layouts.app')

@section('title', 'PFMO Dashboard - Enhanced Workflow')

@push('styles')
<style>
    .pfmo-dashboard {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .stats-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .workflow-stage {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0 8px 8px 0;
    }
    
    .workflow-stage.evaluation {
        border-left-color: #ffc107;
    }
    
    .workflow-stage.decision {
        border-left-color: #28a745;
    }
    
    .workflow-stage.completed {
        border-left-color: #6f42c1;
    }
    
    .sub-dept-card {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #17a2b8;
    }
    
    .priority-urgent {
        background-color: #dc3545;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    
    .priority-rush {
        background-color: #ffc107;
        color: #212529;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    
    .priority-routine {
        background-color: #28a745;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Enhanced PFMO Dashboard Header -->
    <div class="pfmo-dashboard">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">PFMO Enhanced Workflow Dashboard</h1>
                <p class="text-lg opacity-90">Physical Facilities Management Office - Streamlined Process Management</p>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-75">{{ now()->format('F j, Y') }}</div>
                <div class="text-xs opacity-50">{{ now()->format('g:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-card text-center">
            <div class="text-2xl font-bold text-blue-600 mb-2">{{ $dashboard['stats']['total_requests'] ?? 0 }}</div>
            <div class="text-gray-600">Total Requests</div>
        </div>
        
        <div class="stats-card text-center">
            <div class="text-2xl font-bold text-yellow-600 mb-2">{{ $dashboard['stats']['pending_requests'] ?? 0 }}</div>
            <div class="text-gray-600">Pending Approval</div>
        </div>
        
        <div class="stats-card text-center">
            <div class="text-2xl font-bold text-orange-600 mb-2">{{ $dashboard['stats']['under_evaluation'] ?? 0 }}</div>
            <div class="text-gray-600">Under Evaluation</div>
        </div>
        
        <div class="stats-card text-center">
            <div class="text-2xl font-bold text-green-600 mb-2">{{ $dashboard['stats']['approved_today'] ?? 0 }}</div>
            <div class="text-gray-600">Approved Today</div>
        </div>
    </div>

    <!-- Enhanced Workflow Stages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Workflow Process Overview -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Enhanced PFMO Workflow Process</h2>
            
            <div class="workflow-stage">
                <h3 class="font-semibold text-blue-700">1. Initial Request Submission</h3>
                <p class="text-sm text-gray-600 mt-1">Employee submits IOM with auto-department detection</p>
            </div>
            
            <div class="workflow-stage">
                <h3 class="font-semibold text-blue-700">2. Department Head Review</h3>
                <p class="text-sm text-gray-600 mt-1">CCS Dept Head reviews and forwards to PFMO</p>
            </div>
            
            <div class="workflow-stage">
                <h3 class="font-semibold text-blue-700">3. PFMO Head Initial Approval</h3>
                <p class="text-sm text-gray-600 mt-1">PFMO Head evaluates request and assigns to sub-department</p>
            </div>
            
            <div class="workflow-stage evaluation">
                <h3 class="font-semibold text-yellow-700">4. Sub-Department Evaluation</h3>
                <p class="text-sm text-gray-600 mt-1">Specialized team provides technical assessment and feedback</p>
            </div>
            
            <div class="workflow-stage decision">
                <h3 class="font-semibold text-green-700">5. PFMO Head Final Decision</h3>
                <p class="text-sm text-gray-600 mt-1">Final approval based on sub-department recommendation</p>
            </div>
            
            <div class="workflow-stage completed">
                <h3 class="font-semibold text-purple-700">6. Auto Job Order Creation</h3>
                <p class="text-sm text-gray-600 mt-1">Automatic job order generation upon approval</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Recent Activity</h2>
            
            @if($dashboard['recent_requests'] && count($dashboard['recent_requests']) > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($dashboard['recent_requests'] as $request)
                        <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 rounded-r">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $request->title }}</h4>
                                    <p class="text-sm text-gray-600">
                                        From: {{ $request->requester->employeeInfo->FirstName ?? 'Unknown' }} 
                                              {{ $request->requester->employeeInfo->LastName ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $request->date_submitted->diffForHumans() }}</p>
                                </div>
                                <div class="ml-4 flex flex-col items-end space-y-1">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($request->status === 'In Progress') bg-blue-100 text-blue-800
                                        @elseif($request->status === 'Under Sub-Department Evaluation') bg-yellow-100 text-yellow-800
                                        @elseif($request->status === 'Awaiting PFMO Decision') bg-orange-100 text-orange-800
                                        @elseif($request->status === 'Approved') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $request->status }}
                                    </span>
                                    @if($request->iomDetails && $request->iomDetails->priority)
                                        <span class="priority-{{ strtolower($request->iomDetails->priority) }}">
                                            {{ $request->iomDetails->priority }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No recent requests found.</p>
            @endif
        </div>
    </div>

    <!-- Sub-Department Status -->
    @if($dashboard['category_breakdown'] && count($dashboard['category_breakdown']) > 0)
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Request Categories & Sub-Departments</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($dashboard['category_breakdown'] as $category => $count)
                <div class="sub-dept-card">
                    <h3 class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $category) }}</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $count }}</p>
                    <p class="text-sm text-gray-600">Active Requests</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Performance Metrics -->
    @if($dashboard['performance_metrics'])
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Performance Metrics</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    {{ $dashboard['performance_metrics']['average_processing_time_hours'] ?? 0 }}h
                </div>
                <div class="text-gray-600">Avg Processing Time</div>
            </div>
            
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">
                    {{ $dashboard['performance_metrics']['total_processed'] ?? 0 }}
                </div>
                <div class="text-gray-600">Total Processed</div>
            </div>
            
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">
                    {{ round($dashboard['performance_metrics']['efficiency_rating'] ?? 0) }}%
                </div>
                <div class="text-gray-600">Efficiency Rating</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recommendations -->
    @if($recommendations && count($recommendations) > 0)
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">System Recommendations</h2>
        
        <div class="space-y-3">
            @foreach($recommendations as $recommendation)
                <div class="border-l-4 pl-4 py-3 rounded-r
                    @if($recommendation['priority'] === 'high') border-red-500 bg-red-50
                    @elseif($recommendation['priority'] === 'medium') border-yellow-500 bg-yellow-50
                    @else border-blue-500 bg-blue-50
                    @endif">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold 
                                @if($recommendation['priority'] === 'high') text-red-800
                                @elseif($recommendation['priority'] === 'medium') text-yellow-800
                                @else text-blue-800
                                @endif">
                                {{ $recommendation['title'] }}
                            </h4>
                            <p class="text-gray-700 mt-1">{{ $recommendation['message'] }}</p>
                            @if($recommendation['action'])
                                <p class="text-sm text-gray-600 mt-2">
                                    <strong>Suggested Action:</strong> {{ $recommendation['action'] }}
                                </p>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                            @if($recommendation['priority'] === 'high') bg-red-200 text-red-800
                            @elseif($recommendation['priority'] === 'medium') bg-yellow-200 text-yellow-800
                            @else bg-blue-200 text-blue-800
                            @endif">
                            {{ ucfirst($recommendation['priority']) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-8 flex flex-wrap gap-4 justify-center">
        <a href="{{ route('pfmo.facility-requests') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            View All Requests
        </a>
        
        <a href="{{ route('pfmo.metrics') }}" 
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Performance Reports
        </a>
        
        <a href="{{ route('approvals.index') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Approval Queue
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        // In a real implementation, you might want to use AJAX to refresh specific sections
        // For now, we'll just reload the page
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 300000); // 5 minutes

    // Add some interactive behavior
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to stats cards
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '';
            });
        });
    });
</script>
@endpush
