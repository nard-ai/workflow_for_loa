<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Track Request') }} - ID: {{ $formRequest->form_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100 space-y-6">
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    {{-- Request Timeline --}}
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-semibold mb-4">Request Timeline</h3>
                        {{-- Removed Print Button for admin view --}}
                        <div class="relative">
                            {{-- Timeline line --}}
                            <div class="absolute h-full w-1 bg-gradient-to-b from-blue-500 via-blue-300 to-gray-200 left-4 top-4"></div>
                            
                            <ul class="space-y-8 relative">
                                {{-- Initial submission --}}
                                <li class="flex items-start">
                                    <div class="flex items-center justify-center">
                                        <div class="bg-blue-500 rounded-full w-8 h-8 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-6">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">Request Submitted by {{ $formRequest->requester->username }} ({{ $formRequest->requester->employeeInfo->FirstName ?? '' }} {{ $formRequest->requester->employeeInfo->LastName ?? '' }})</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $formRequest->date_submitted->format('M j, Y') }} at {{ $formRequest->date_submitted->format('g:i A') }}
                                        </div>
                                    </div>
                                </li>

                                {{-- Approval history --}}
                                @foreach ($formRequest->approvals->sortBy('action_date') as $approval)
                                    @if($approval->action !== 'Submitted' && $approval->action !== 'Evaluate')
                                        <li class="flex items-start">
                                            <div class="flex items-center justify-center">
                                                <div class="
                                                    @if($approval->action === 'Rejected') bg-red-500
                                                    @elseif($approval->action === 'Approved') bg-green-500
                                                    @elseif($approval->action === 'Send Feedback') bg-green-500
                                                    @else bg-gray-500
                                                    @endif
                                                    rounded-full w-8 h-8 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                                    @if($approval->action === 'Rejected')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @elseif($approval->action === 'Approved')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @elseif($approval->action === 'Approved')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-6">
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $approval->action }} by {{ $approval->approver->employeeInfo->FirstName ?? 'N/A' }} {{ $approval->approver->employeeInfo->LastName ?? '' }}
                                                    @if($approval->approver->position === 'Head')
                                                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Head)</span>
                                                    @endif
                                                </div>
                                                @if($approval->comments)
                                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                                        "{{ $approval->comments }}"
                                                    </div>
                                                @endif
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($approval->action_date)->format('M j, Y') }} at {{ \Carbon\Carbon::parse($approval->action_date)->format('g:i A') }}
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Show Completed status after all approvals --}}
                                @if($formRequest->status === 'Approved' && $formRequest->approvals->where('action', 'Approved')->isNotEmpty())
                                    <li class="flex items-start">
                                        <div class="flex items-center justify-center">
                                            <div class="bg-green-500 rounded-full w-8 h-8 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-6">
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                Completed
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($formRequest->approvals->where('action', 'Approved')->sortByDesc('action_date')->first()->action_date)->setTimezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                    </li>
                                @elseif($formRequest->status === 'Rejected' && $formRequest->approvals->where('action', 'Rejected')->isNotEmpty())
                                     <li class="flex items-start">
                                        <div class="flex items-center justify-center">
                                            <div class="bg-red-500 rounded-full w-8 h-8 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-6">
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                Request {{ $formRequest->status }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                 {{ \Carbon\Carbon::parse($formRequest->approvals->where('action', 'Rejected')->sortByDesc('action_date')->first()->action_date)->setTimezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                {{-- Current status if not completed/rejected --}}
                                @if(!in_array($formRequest->status, ['Approved', 'Rejected', 'Cancelled']))
                                    <li class="flex items-start">
                                        <div class="flex items-center justify-center">
                                            <div class="bg-yellow-500 rounded-full w-8 h-8 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-6">
                                            @if($formRequest->status === 'Under Sub-Department Evaluation')
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">Currently Under Sub-Department Evaluation</div>
                                                @php
                                                    $subDepartment = null;
                                                    if($formRequest->assigned_sub_department) {
                                                        $subDepartment = $formRequest->assigned_sub_department;
                                                    } elseif($formRequest->currentApprover && $formRequest->currentApprover->department) {
                                                        $subDepartment = $formRequest->currentApprover->department->dept_name;
                                                    }
                                                @endphp
                                                @if($subDepartment)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        Sub-Department: {{ ucwords(str_replace('_', ' ', $subDepartment)) }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">Currently {{ $formRequest->status }}</div>
                                                @if($formRequest->currentApprover)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if($formRequest->currentApprover->position === 'VPAA' || ($formRequest->currentApprover->department && $formRequest->currentApprover->department->dept_code === 'VPAA'))
                                                            Awaiting action from {{ $currentApprover->employeeInfo->FirstName ?? '' }} {{ $currentApprover->employeeInfo->LastName ?? '' }} ({{ $currentApprover->username }})
                                                        @else
                                                            Awaiting action from {{ $formRequest->currentApprover->employeeInfo->FirstName ?? $formRequest->currentApprover->username }} {{ $formRequest->currentApprover->employeeInfo->LastName ?? '' }}
                                                        @endif
                                                    </div>
                                                @elseif($formRequest->status === 'Pending' && $formRequest->approvals->isEmpty()) {{-- Or specific initial pending state --}}
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        Request is pending initial processing.
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- Requester Information --}}
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-semibold">Requester Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 text-sm">
                            <p><strong>Name:</strong> {{ $formRequest->requester->employeeInfo->FirstName ?? 'N/A' }} {{ $formRequest->requester->employeeInfo->LastName ?? '' }}</p>
                            <p><strong>Username:</strong> {{ $formRequest->requester->username }}</p>
                            <p><strong>Department:</strong> {{ $formRequest->requester->department->dept_name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $formRequest->requester->employeeInfo->Email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- IOM Specific Details --}}
                    @if ($formRequest->form_type === 'IOM' && $formRequest->iomDetails)
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold">IOM Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 text-sm">
                                <p><strong>From Department:</strong> {{ $formRequest->fromDepartment->dept_name ?? 'N/A' }} ({{ $formRequest->fromDepartment->dept_code ?? 'N/A' }})</p>
                                <p><strong>To Department:</strong> {{ $formRequest->toDepartment->dept_name ?? 'N/A' }} ({{ $formRequest->toDepartment->dept_code ?? 'N/A' }})</p>
                                <p><strong>Subject/Re:</strong> {{ $formRequest->title }}</p>
                                <p><strong>Date Needed:</strong> {{ $formRequest->iomDetails->date_needed ? \Carbon\Carbon::parse($formRequest->iomDetails->date_needed)->format('M j, Y') : 'N/A' }}</p>
                                <p><strong>Priority:</strong> {{ $formRequest->iomDetails->priority ?? 'N/A' }}</p>
                                <p class="md:col-span-2"><strong>Purpose:</strong> {{ $formRequest->iomDetails->purpose ?? 'N/A' }}</p>
                                <div class="md:col-span-2">
                                    <p class="font-semibold">Description/Body:</p>
                                    <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md whitespace-pre-wrap">{{ $formRequest->iomDetails->body ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Signatures Section for IOM --}}
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Signatures</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($formRequest->approvals->sortBy('action_date') as $approval)
                                    @if($approval->action !== 'Submitted' && $approval->action !== 'Evaluate')
                                        <div class="signature-card bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                            @if($approval->signature_data && (strpos($approval->signature_data, 'data:image/') === 0 || filter_var($approval->signature_data, FILTER_VALIDATE_URL)))
                                                <div class="signature-image-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700">
                                                    <img src="{{ $approval->signature_data }}"
                                                         alt="Digital Signature"
                                                         class="max-h-20 object-contain">
                                                </div>
                                            @elseif($approval->signature_name && $approval->approver && $approval->approver->signatureStyle)
                                                <div class="signature-text-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 px-2">                                                    <div class="text-xl font-signature text-center" style="font-family: '{{ $approval->approver->signatureStyle->font_family }}', cursive; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; line-height: 1.2; letter-spacing: 0.5px; padding: 10px 0;">
                                                        {{ strtoupper($approval->signature_name) }}
                                                    </div>
                                                </div>
                                            @elseif($approval->signature_name)
                                                <div class="signature-text-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 px-2">                                                    <div class="text-xl font-signature text-center" style="font-family: 'Dancing Script', cursive; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; line-height: 1.2; letter-spacing: 0.5px; padding: 10px 0;">
                                                        {{ strtoupper($approval->signature_name) }}
                                                    </div>
                                                </div>
                                            @else {{-- Fallback if no signature data or name but approval exists --}}
                                                <div class="signature-text-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 px-2">
                                                    <div class="text-sm text-gray-500 italic">No signature image/style</div>
                                                </div>
                                            @endif
                                            <div class="mt-3 text-center">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $approval->approver->employeeInfo->FirstName ?? 'N/A' }} {{ $approval->approver->employeeInfo->LastName ?? '' }}
                                                </p>
                                                <div class="flex items-center justify-center gap-2 mt-1">
                                                    <span class="text-xs px-2 py-1 rounded-full {{ 
                                                        $approval->action === 'Approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                        ($approval->action === 'Rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') 
                                                    }}">
                                                        {{ $approval->action }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($approval->action_date)->format('M j, Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Leave Specific Details --}}
                    @if ($formRequest->form_type === 'Leave' && $formRequest->leaveDetails)
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold mb-4">Leave Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="font-medium">Leave Type:</p>
                                    <p class="text-gray-800 dark:text-gray-200">{{ ucfirst($formRequest->leaveDetails->leave_type) }} Leave</p>
                                </div>
                                <div>
                                    <p class="font-medium">Duration:</p>
                                    <p class="text-gray-800 dark:text-gray-200">{{ $formRequest->leaveDetails->days }} day(s)</p>
                                </div>
                                <div>
                                    <p class="font-medium">Start Date:</p>
                                    <p class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($formRequest->leaveDetails->start_date)->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">End Date:</p>
                                    <p class="text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($formRequest->leaveDetails->end_date)->format('F j, Y') }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="font-medium">Description / Reason:</p>
                                    <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap bg-gray-50 dark:bg-gray-700 p-3 rounded-md mt-1">{{ $formRequest->leaveDetails->description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Signatures Section for Leave -->
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold mb-4">Signatures</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($formRequest->approvals->sortBy('action_date') as $approval)
                                    @if($approval->action !== 'Submitted' && $approval->action !== 'Evaluate')
                                    <div class="signature-card bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                        @if($approval->signature_data && (strpos($approval->signature_data, 'data:image/') === 0 || filter_var($approval->signature_data, FILTER_VALIDATE_URL)))
                                            <div class="signature-image-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700">
                                                <img src="{{ $approval->signature_data }}" alt="Digital Signature" class="max-h-20 object-contain">
                                            </div>
                                        @elseif($approval->signature_name && $approval->approver && $approval->approver->signatureStyle)
                                            <div class="signature-text-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 px-2">
                                                <div class="text-xl font-signature text-center" style="font-family: '{{ $approval->approver->signatureStyle->font_family }}', cursive; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">{{ strtoupper($approval->signature_name) }}</div>
                                            </div>
                                        @elseif($approval->signature_name)
                                            <div class="signature-text-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 px-2">
                                                <div class="text-xl font-signature text-center" style="font-family: 'Dancing Script', cursive; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">{{ strtoupper($approval->signature_name) }}</div>
                                            </div>
                                        @else {{-- Fallback for Leave signatures --}}
                                            <div class="signature-text-container h-24 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 px-2">
                                                 <div class="text-sm text-gray-500 italic">No signature image/style</div>
                                            </div>
                                        @endif
                                        <div class="mt-3 text-center">
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $approval->approver->employeeInfo->FirstName ?? 'N/A' }} {{ $approval->approver->employeeInfo->LastName ?? '' }}</p>
                                            <div class="flex items-center justify-center gap-2 mt-1">
                                                <span class="text-xs px-2 py-1 rounded-full {{ 
                                                    $approval->action === 'Approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                    ($approval->action === 'Rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') 
                                                }}">
                                                    {{ $approval->action }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($approval->action_date)->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Back to Dashboard Link --}}
                    <div class="mt-6">
                        <a href="{{ route('admin.dashboard') }}" 
                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg transition-all duration-200 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-300 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Admin Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Mr+Dafoe&family=Homemade+Apple&family=Pacifico&family=Dancing+Script&display=swap');

    .font-signature {
        /* Base signature style, specific font-family will be applied inline */
    }
</style>
