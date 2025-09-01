    <!-- Custom CSS for Signature Block -->
    <style>
        .signature-block {
            background: linear-gradient(145deg, #f8fafc, #f1f5f9);
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .dark .signature-block {
            background: linear-gradient(145deg, #374151, #4b5563);
            border: 1px solid #6b7280;
        }
        .signature-line {
            border-top: 2px solid #374151;
            position: relative;
        }
        .dark .signature-line {
            border-top: 2px solid #9ca3af;
        }
        .signature-name {
            letter-spacing: 0.1em;
            font-weight: 700;
        }
        .signature-title {
            font-size: 0.75rem;
            color: #6b7280;
        }
        .dark .signature-title {
            color: #9ca3af;
        }
    </style>

    <!-- Approval Error Modal -->
    <div id="approvalErrorModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 hidden overflow-y-auto h-full w-full z-70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 relative">
            <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Approval Error</h3>
                    <button type="button" onclick="closeApprovalErrorModal()" class="text-white hover:text-gray-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="approvalErrorMessage" class="text-red-700 dark:text-red-300 text-base font-medium"></div>
            </div>
        </div>
    </div>
<script>
function showApprovalError(message) {
    const modal = document.getElementById('approvalErrorModal');
    const msgDiv = document.getElementById('approvalErrorMessage');
    if (modal && msgDiv) {
        msgDiv.innerHTML = message;
        modal.classList.remove('hidden');
    }
}

function showValidationError(message) {
    // Create validation alert element with different styling
    const validationAlert = document.createElement('div');
    validationAlert.className = 'fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-lg z-50 max-w-md';
    validationAlert.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-yellow-800">Required Information</h3>
                <div class="mt-1 text-sm text-yellow-700">${message}</div>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-yellow-400 hover:text-yellow-600">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(validationAlert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (validationAlert.parentNode) {
            validationAlert.remove();
        }
    }, 5000);
}
function closeApprovalErrorModal() {
    const modal = document.getElementById('approvalErrorModal');
    if (modal) modal.classList.add('hidden');
}
// Improved tab navigation logic for signature method
document.addEventListener('DOMContentLoaded', function() {
    window.showSignatureTab = function(tab) {
        var textTab = document.getElementById('textSignatureTabShow');
        var drawTab = document.getElementById('drawSignatureTabShow');
        var textMethod = document.getElementById('textSignatureMethodShow');
        var drawMethod = document.getElementById('drawSignatureMethodShow');
        if (tab === 'text') {
            textTab.classList.add('border-blue-500', 'text-blue-600', 'bg-white');
            textTab.classList.remove('text-gray-500', 'bg-gray-100');
            drawTab.classList.remove('border-blue-500', 'text-blue-600', 'bg-white');
            drawTab.classList.add('text-gray-500', 'bg-gray-100');
            textMethod.classList.remove('hidden');
            drawMethod.classList.add('hidden');
        } else {
            drawTab.classList.add('border-blue-500', 'text-blue-600', 'bg-white');
            drawTab.classList.remove('text-gray-500', 'bg-gray-100');
            textTab.classList.remove('border-blue-500', 'text-blue-600', 'bg-white');
            textTab.classList.add('text-gray-500', 'bg-gray-100');
            drawMethod.classList.remove('hidden');
            textMethod.classList.add('hidden');
        }
    };
    // Set initial tab
    showSignatureTab('text');
});
// Tab navigation logic for signature method
function showSignatureTab(tab) {
    var textTab = document.getElementById('textSignatureTabShow');
    var drawTab = document.getElementById('drawSignatureTabShow');
    var textMethod = document.getElementById('textSignatureMethodShow');
    var drawMethod = document.getElementById('drawSignatureMethodShow');
    if (tab === 'text') {
        textTab.classList.add('border-blue-500', 'text-blue-600');
        drawTab.classList.remove('border-blue-500', 'text-blue-600');
        textTab.classList.remove('text-gray-500');
        drawTab.classList.add('text-gray-500');
        textMethod.classList.remove('hidden');
        drawMethod.classList.add('hidden');
    } else {
        drawTab.classList.add('border-blue-500', 'text-blue-600');
        textTab.classList.remove('border-blue-500', 'text-blue-600');
        drawTab.classList.remove('text-gray-500');
        textTab.classList.add('text-gray-500');
        drawMethod.classList.remove('hidden');
        textMethod.classList.add('hidden');
    }
}
function closeModal() {
    var modal = document.getElementById('actionModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function showSuccessMessage(message) {
    // Create success alert element
    const successAlert = document.createElement('div');
    successAlert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center space-x-3 transform transition-all duration-300 translate-x-full';
    successAlert.innerHTML = `
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-medium">${message}</span>
    `;
    
    // Add to page
    document.body.appendChild(successAlert);
    
    // Animate in
    setTimeout(() => {
        successAlert.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        successAlert.classList.add('translate-x-full');
        setTimeout(() => {
            if (successAlert.parentNode) {
                successAlert.parentNode.removeChild(successAlert);
            }
        }, 300);
    }, 3000);
}
</script>
<!-- Feedback Final Confirmation Modal -->
<div id="feedbackFinalConfirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 hidden overflow-y-auto h-full w-full z-80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-[450px] max-w-lg shadow-2xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden transform transition-all duration-300">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Confirm Feedback Submission
            </h3>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Your Feedback:</label>
                <div id="finalFeedbackPreview" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 min-h-[100px] max-h-[200px] overflow-y-auto text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed"></div>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button" onclick="closeFinalFeedbackConfirmModal()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-all duration-200">Go Back & Edit</button>
                <button type="button" onclick="submitFinalFeedback()" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg transition-all duration-200">Yes, Submit Feedback</button>
            </div>
        </div>
    </div>
</div>
<script>
function openFeedbackConfirmModal() {
    var modal = document.getElementById('feedbackConfirmModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}
function closeFeedbackConfirmModal() {
    var modal = document.getElementById('feedbackConfirmModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>
<script>
// Feedback Modal Logic
document.addEventListener('DOMContentLoaded', function() {
    // Feedback input and preview

    var feedbackInput = document.getElementById('feedbackInput');
    var confirmCharCount = document.getElementById('confirmCharCount');
    var readyText = document.querySelector('#feedbackConfirmModal .text-green-600');
    var submitBtn = document.getElementById('submitFeedbackBtn');
    var feedbackForm = document.getElementById('feedbackForm');
    var feedbackFormComments = document.getElementById('feedbackFormComments');
    var shortWarningModal = document.getElementById('shortFeedbackWarningModal');
    var shortCharCount = document.getElementById('shortFeedbackCharCount');

    function updateCountAndValidation() {
        var val = feedbackInput.value;
        var isValid = true;
        var validationMessages = [];
        
        // Character count
        confirmCharCount.textContent = val.length + ' characters';
        
        // Length validation
        if (val.length < 10) {
            confirmCharCount.classList.add('text-red-500');
            validationMessages.push('At least 10 characters required');
            isValid = false;
        } else if (val.length > 2000) {
            confirmCharCount.classList.add('text-red-500');
            validationMessages.push('Maximum 2000 characters allowed');
            isValid = false;
        } else {
            confirmCharCount.classList.remove('text-red-500');
        }
        
        // Duplicate character validations
        if (val.length > 0) {
            // Check for excessive repeated characters (more than 3 consecutive)
            if (/(.)\1{3,}/.test(val)) {
                validationMessages.push('Cannot contain more than 3 consecutive identical characters');
                isValid = false;
            }
            
            // Check for repeated patterns
            if (/(.{2,})\1{2,}/.test(val)) {
                validationMessages.push('Cannot contain excessively repeated text patterns');
                isValid = false;
            }
            
            // Check character variety (more than 50% same character)
            var chars = val.toLowerCase().replace(/\s+/g, '').split('');
            if (chars.length > 0) {
                var charCounts = {};
                chars.forEach(function(char) {
                    charCounts[char] = (charCounts[char] || 0) + 1;
                });
                var maxCount = Math.max(...Object.values(charCounts));
                if (maxCount / chars.length > 0.5) {
                    validationMessages.push('Feedback must contain more varied content');
                    isValid = false;
                }
            }
            
            // Check for alphabetic characters
            if (!/[a-zA-Z]/.test(val)) {
                validationMessages.push('Must contain alphabetic characters');
                isValid = false;
            }
            
            // Check word count (minimum 3 words)
            var wordCount = val.trim().split(/\s+/).filter(word => word.length > 0).length;
            if (wordCount < 3) {
                validationMessages.push('Must contain at least 3 words');
                isValid = false;
            }
        }
        
        // Update validation display
        var validationDisplay = document.getElementById('feedbackValidationMessages');
        if (!validationDisplay) {
            validationDisplay = document.createElement('div');
            validationDisplay.id = 'feedbackValidationMessages';
            validationDisplay.className = 'mt-2 text-sm';
            feedbackInput.parentNode.appendChild(validationDisplay);
        }
        
        if (validationMessages.length > 0) {
            validationDisplay.innerHTML = validationMessages.map(msg => 
                `<div class="text-red-500 flex items-center mb-1">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    ${msg}
                </div>`
            ).join('');
            readyText.textContent = '';
            submitBtn.disabled = true;
        } else if (val.length >= 10) {
            validationDisplay.innerHTML = ''; // Clear validation messages when valid
            readyText.textContent = '✓ Ready to submit';
            submitBtn.disabled = false;
        } else {
            validationDisplay.innerHTML = '';
            readyText.textContent = '';
            submitBtn.disabled = false;
        }
    }
    feedbackInput.addEventListener('input', updateCountAndValidation);
    updateCountAndValidation();

    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var val = feedbackInput.value;
        
        // Comprehensive validation before showing any modal
        var validationResult = validateFeedbackContent(val);
        
        if (!validationResult.isValid) {
            // Show validation errors
            alert('Please fix the following issues:\n' + validationResult.errors.join('\n'));
            return;
        }
        
        if (val.length < 10) {
            if (shortWarningModal && shortCharCount) {
                shortCharCount.textContent = val.length;
                shortWarningModal.classList.remove('hidden');
            }
        } else if (val.length < 20) {
            if (shortWarningModal && shortCharCount) {
                shortCharCount.textContent = val.length;
                shortWarningModal.classList.remove('hidden');
            }
        } else {
            // Show confirmation modal
            document.getElementById('finalFeedbackPreview').textContent = val;
            // Close the original feedback modal first
            closeFeedbackConfirmModal();
            // Add a small delay for smooth transition
            setTimeout(function() {
                document.getElementById('feedbackFinalConfirmModal').classList.remove('hidden');
            }, 150);
        }
    });

    // Validation helper function
    function validateFeedbackContent(text) {
        var errors = [];
        
        // Check for excessive repeated characters
        if (/(.)\1{3,}/.test(text)) {
            errors.push('Cannot contain more than 3 consecutive identical characters');
        }
        
        // Check for repeated patterns
        if (/(.{2,})\1{2,}/.test(text)) {
            errors.push('Cannot contain excessively repeated text patterns');
        }
        
        // Check alphabetic content
        if (!/[a-zA-Z]/.test(text)) {
            errors.push('Must contain alphabetic characters');
        }
        
        // Check word count
        var wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;
        if (wordCount < 3 && text.length >= 10) {
            errors.push('Must contain at least 3 words');
        }
        
        // Check character variety
        var chars = text.toLowerCase().replace(/\s+/g, '').split('');
        if (chars.length > 0) {
            var charCounts = {};
            chars.forEach(function(char) {
                charCounts[char] = (charCounts[char] || 0) + 1;
            });
            var maxCount = Math.max(...Object.values(charCounts));
            if (maxCount / chars.length > 0.5) {
                errors.push('Must contain more varied content');
            }
        }
        
        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    window.closeFinalFeedbackConfirmModal = function() {
        document.getElementById('feedbackFinalConfirmModal').classList.add('hidden');
    };
    window.submitFinalFeedback = function() {
        var val = feedbackInput.value;
        feedbackFormComments.value = val;
        document.getElementById('feedbackFinalConfirmModal').classList.add('hidden');
        // Also ensure the original feedback modal is closed
        closeFeedbackConfirmModal();
        feedbackForm.submit();
    };
    window.closeShortFeedbackWarning = function() {
        if (shortWarningModal) shortWarningModal.classList.add('hidden');
    };
    window.proceedWithShortFeedback = function() {
        var val = feedbackInput.value;
        
        // Perform final validation before submission
        if (val.length < 10) {
            alert('Feedback must be at least 10 characters long.');
            return;
        }
        
        // Check for duplicate character issues even in short feedback
        if (/(.)\1{3,}/.test(val)) {
            alert('Feedback cannot contain more than 3 consecutive identical characters.');
            return;
        }
        
        if (!/[a-zA-Z]/.test(val)) {
            alert('Feedback must contain alphabetic characters.');
            return;
        }
        
        // Set the feedback value and submit
        feedbackFormComments.value = val;
        
        // Close modals
        closeShortFeedbackWarning();
        closeFeedbackConfirmModal();
        
        // Submit the form
        feedbackForm.submit();
    };
});
</script>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Review Request Details') }} - ID: {{ $formRequest->form_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <div class="font-medium">Please correct the following errors:</div>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100 space-y-6">
                    {{-- Request Timeline --}}
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-semibold mb-4">Request Timeline</h3>
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
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">Submitted by {{ $formRequest->requester->username }}</div>
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
                                                    @elseif($approval->action === 'Evaluate') bg-blue-500
                                                    @elseif($approval->action === 'Send Feedback') bg-green-500
                                                    @else bg-gray-500
                                                    @endif
                                                    rounded-full w-8 h-8 flex items-center justify-center ring-4 ring-white dark:ring-gray-800
                                                ">
                                                    @if($approval->action === 'Rejected')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @elseif($approval->action === 'Approved')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @elseif($approval->action === 'Evaluate')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                        </svg>
                                                    @elseif($approval->action === 'Send Feedback')
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-6">
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $approval->action }} by {{ $approval->approver->employeeInfo->FirstName }} {{ $approval->approver->employeeInfo->LastName }}
                                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $approval->approver->position }})</span>
                                                </div>
                                                
                                                @if($approval->comments)
                                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                                        "{{ $approval->comments }}"
                                                    </div>
                                                @endif
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($approval->action_date)->setTimezone('Asia/Manila')->format('M j, Y') }} at {{ \Carbon\Carbon::parse($approval->action_date)->setTimezone('Asia/Manila')->format('g:i A') }}
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Current status if not completed --}}
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
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">Currently {{ $formRequest->status }}</div>
                                            @if($formRequest->status === 'Under Sub-Department Evaluation')
                                                {{-- For PFMO sub-department evaluation, show the sub-department name --}}
                                                @php
                                                    $subDeptName = 'PFMO Sub-Department';
                                                    if ($formRequest->assigned_sub_department) {
                                                        switch($formRequest->assigned_sub_department) {
                                                            case 'electrical':
                                                                $subDeptName = 'PFMO Electrical Department';
                                                                break;
                                                            case 'hvac':
                                                                $subDeptName = 'PFMO HVAC Department';
                                                                break;
                                                            case 'general_services':
                                                                $subDeptName = 'PFMO General Services';
                                                                break;
                                                            default:
                                                                $subDeptName = 'PFMO ' . ucwords(str_replace('_', ' ', $formRequest->assigned_sub_department)) . ' Department';
                                                        }
                                                    }
                                                @endphp
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    Awaiting action from {{ $subDeptName }}
                                                </div>
                                            @elseif($currentApprover = App\Models\User::with('employeeInfo', 'department')->find($formRequest->current_approver_id))
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    @if($currentApprover->position === 'VPAA' || ($currentApprover->department && $currentApprover->department->dept_code === 'VPAA'))
                                                        Awaiting action from {{ $currentApprover->employeeInfo->FirstName ?? '' }} {{ $currentApprover->employeeInfo->LastName ?? '' }} ({{ $currentApprover->username }})
                                                    @else
                                                        Awaiting action from {{ $currentApprover->username }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- Request Details --}}
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-semibold">Request Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 text-sm">
                            <p><strong>Request ID:</strong> {{ $formRequest->form_id }}</p>
                            <p><strong>Type:</strong> {{ $formRequest->form_type }}</p>
                            <p><strong>Requester:</strong> {{ $formRequest->requester->employeeInfo->FirstName }} {{ $formRequest->requester->employeeInfo->LastName }}</p>
                            <p><strong>Requester's Department:</strong> {{ $formRequest->requester->department->dept_name ?? 'N/A' }} ({{ $formRequest->requester->department->dept_code ?? 'N/A' }})</p>
                            <p><strong>Date Submitted:</strong> {{ $formRequest->date_submitted->format('Y-m-d H:i A') }}</p>
                            <p><strong>Current Status:</strong> <span class="font-semibold">{{ $formRequest->status }}</span></p>
                        </div>
                    </div>

                    {{-- IOM Specific Details --}}
                    @if ($formRequest->form_type === 'IOM' && $formRequest->iomDetails)
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold">IOM Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 text-sm">
                                <p><strong>To Department:</strong> {{ $formRequest->toDepartment->dept_name ?? 'N/A' }} ({{ $formRequest->toDepartment->dept_code ?? 'N/A' }})</p>
                                <p><strong>Subject/Re:</strong> {{ $formRequest->title }}</p>
                                <p><strong>Date Needed:</strong> {{ $formRequest->iomDetails->date_needed ? \Carbon\Carbon::parse($formRequest->iomDetails->date_needed)->format('Y-m-d') : 'N/A' }}</p>
                                <p><strong>Priority:</strong> {{ $formRequest->iomDetails->priority ?? 'N/A' }}</p>
                                <p class="md:col-span-2"><strong>Purpose:</strong> {{ $formRequest->iomDetails->purpose ?? 'N/A' }}</p>
                                <div class="md:col-span-2">
                                    <p class="font-semibold">Description/Body:</p>
                                    <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md whitespace-pre-wrap">{{ $formRequest->iomDetails->body ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Leave Specific Details --}}
                    @if ($formRequest->form_type === 'Leave' && $formRequest->leaveDetails)
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold">Leave Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 text-sm">
                                <p><strong>Leave Type:</strong> {{ ucfirst($formRequest->leaveDetails->leave_type ?? 'N/A') }}</p>
                                <p><strong>Duration:</strong> {{ $formRequest->leaveDetails->days }} day(s)</p>
                                <p><strong>Start Date:</strong> {{ $formRequest->leaveDetails->start_date ? $formRequest->leaveDetails->start_date->format('F j, Y') : 'N/A' }}</p>
                                <p><strong>End Date:</strong> {{ $formRequest->leaveDetails->end_date ? $formRequest->leaveDetails->end_date->format('F j, Y') : 'N/A' }}</p>
                                <div class="md:col-span-2">
                                    <p class="font-semibold">Description / Reason:</p>
                                    <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md whitespace-pre-wrap">{{ $formRequest->leaveDetails->description ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Approval Signatures Section --}}
                    @php
                        $finalApprovals = $formRequest->approvals->filter(function($approval) {
                            return in_array($approval->action, ['Approved', 'Rejected']);
                        });
                    @endphp

                    @if ($finalApprovals->count() > 0)
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold mb-4">Signatures</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($finalApprovals->sortBy('action_date') as $approval)
                                    @php
                                        $approverUser = $approval->approver;
                                        $displayName = $approval->signature_name ?: ($approverUser && $approverUser->employeeInfo ? $approverUser->employeeInfo->FirstName . ' ' . $approverUser->employeeInfo->LastName : 'N/A');
                                        $isBase64 = isset($approval->signature_data) && \Illuminate\Support\Str::startsWith($approval->signature_data, ['data:image/png;base64,', 'data:image/jpeg;base64,']);
                                        $isUrl = isset($approval->signature_data) && filter_var($approval->signature_data, FILTER_VALIDATE_URL);
                                    @endphp
                                    
                                    {{-- Traditional Signature Block --}}
                                    <div class="signature-block relative bg-gradient-to-b from-gray-50 to-white border border-gray-200 rounded-lg p-6 h-48 flex flex-col justify-between shadow-sm">
                                        {{-- Signature Area --}}
                                        <div class="signature-area flex-grow flex items-center justify-center mb-3">
                                            @if (!empty($approval->signature_data) && ($isBase64 || $isUrl))
                                                {{-- Drawn Digital Signature --}}
                                                <img src="{{ $approval->signature_data }}" alt="Digital Signature" class="max-w-full max-h-16 object-contain filter drop-shadow-sm">
                                            @elseif (!empty($approval->signature_name))
                                                {{-- Text Style Signature --}}
                                                <div class="text-signature-display">
                                                    @php
                                                        // Get signature style from signature_style_choice (database ID)
                                                        $signatureStyleId = $approval->signature_style_choice ?? $approval->signature_style_id;
                                                        $signatureStyle = null;
                                                        
                                                        // Find the signature style in the database
                                                        if ($signatureStyleId) {
                                                            $signatureStyle = $signatureStyles->firstWhere('id', $signatureStyleId);
                                                        }
                                                    @endphp
                                                    
                                                    @if($signatureStyle)
                                                        @if($signatureStyle->name === 'Formal Bold')
                                                            <div class="database-signature-style text-xl font-bold text-gray-800" style="font-family: '{{ $signatureStyle->font_family }}', serif; text-decoration: underline; text-transform: uppercase;">
                                                                {{ $approval->signature_name }}
                                                            </div>
                                                        @elseif($signatureStyle->name === 'Typewriter')
                                                            <div class="database-signature-style text-lg font-mono text-gray-800" style="font-family: '{{ $signatureStyle->font_family }}', monospace; letter-spacing: 2px; text-transform: uppercase;">
                                                                {{ $approval->signature_name }}
                                                            </div>
                                                        @else
                                                            <div class="database-signature-style text-xl text-gray-800" style="font-family: '{{ $signatureStyle->font_family }}', cursive; text-transform: uppercase;">
                                                                {{ $approval->signature_name }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        {{-- Fallback if no style found --}}
                                                        <div class="signature-default text-xl font-serif italic text-gray-800" style="text-transform: uppercase;">
                                                            {{ $approval->signature_name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="signature-placeholder text-gray-400 italic text-sm border-b border-gray-300 pb-1 min-w-32 text-center">
                                                    Digital Signature
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Name Line --}}
                                        <div class="name-line border-t border-gray-400 pt-2 text-center">
                                            <div class="font-serif text-sm font-medium text-gray-800 tracking-wide">
                                                {{ $displayName }}
                                            </div>
                                            <div class="text-xs text-gray-600 mt-1">
                                                @if ($approverUser)
                                                    {{ $approverUser->position }}
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Status Badge --}}
                                        <div class="absolute top-2 right-2">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($approval->action === 'Rejected') bg-red-100 text-red-700 border border-red-200
                                                @elseif($approval->action === 'Approved') bg-green-100 text-green-700 border border-green-200
                                                @else bg-gray-100 text-gray-700 border border-gray-200
                                                @endif">
                                                {{ $approval->action }}
                                            </span>
                                        </div>
                                        
                                        {{-- Date --}}
                                        <div class="absolute bottom-1 left-2 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($approval->action_date)->setTimezone(config('app.timezone_display', 'Asia/Manila'))->format('M j, Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Approval Actions --}}
                    @if($canTakeAction)
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-semibold mb-4">Take Action</h3>
                            <div class="flex space-x-4">
                                @php
                                    $user = Auth::user();
                                    $isPFMOHead = $user->position === 'PFMO Head' || 
                                                  ($user->department && $user->department->dept_code === 'PFMO' && $user->position === 'Head');
                                @endphp

                                @if($canEvaluate)
                                    {{-- PFMO Head gets Evaluate and Reject options --}}
                                    <button onclick="confirmEvaluate()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                        Evaluate
                                    </button>
                                    <button onclick="openActionModal('reject')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                        Reject
                                    </button>
                                @elseif($canSendFeedback)
                                    {{-- PFMO Sub-department staff get Send Feedback option only (no reject) --}}
                                    <button onclick="openFeedbackConfirmModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                        Send Feedback
                                    </button>
                                @elseif($canFinalDecision)
                                    {{-- PFMO Head gets final Approve and Reject options after receiving feedback --}}
                                    <button onclick="openActionModal('approve')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                        Approve
                                    </button>
                                    <button onclick="openActionModal('reject')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                        Reject
                                    </button>
                                @else
                                    {{-- Standard workflow for non-PFMO users --}}
                                    @if($formRequest->form_type === 'IOM')
                                        @if($formRequest->status === 'Pending')
                                            {{-- Approve Button for approvers --}}
                                            <button onclick="openActionModal('approve')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                                Approve
                                            </button>
                                        @endif

                                        @if(in_array($formRequest->status, ['In Progress', 'Pending Target Department Approval']))
                                            {{-- Approve Button for target department approvers --}}
                                            <button onclick="openActionModal('approve')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                                Approve
                                            </button>
                                        @endif
                                    @else
                                        {{-- For Leave requests --}}
                                        @if($formRequest->status === 'Pending')
                                            {{-- Approve Button for department heads --}}
                                            <button onclick="openActionModal('approve')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                                Approve
                                            </button>
                                        @else
                                            {{-- Approve Button for HR --}}
                                            <button onclick="openActionModal('approve')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                                Approve
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Reject Button (always available for non-PFMO workflow) --}}
                                    <button onclick="openActionModal('reject')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                        Reject
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                @if(Auth::user()->accessRole === 'Viewer')
                                    You are viewing this request in read-only mode. Only users with Approver role can take actions on requests.
                                @else
                                    You cannot take action on this request at this time. The request may be in a status that doesn't require your action, or it may need action from a different department.
                                @endif
                            </p>
                        </div>
                    @endif

                    {{-- Custom Evaluate Confirmation Modal --}}
                    <div id="evaluateConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
                        <div class="relative top-20 mx-auto p-6 border-0 w-96 shadow-2xl rounded-xl bg-white dark:bg-gray-800">
                            <div class="text-center">
                                <!-- Icon with pulse animation -->
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 mb-6 animate-pulse">
                                    <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Title -->
                                <h3 class="text-xl leading-6 font-semibold text-gray-900 dark:text-white mb-3">
                                    Evaluate Request
                                </h3>
                                
                                <!-- Message -->
                                <div class="mt-2 px-4 py-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                        This will evaluate the request and automatically route it to the designated sub-department for further processing.
                                    </p>
                                </div>
                                
                                <!-- Info Badge -->
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-300 mb-6">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    No signature required for evaluation
                                </div>
                                
                                <!-- Buttons -->
                                <div class="flex justify-center space-x-4 px-4 py-3">
                                    <button id="cancelEvaluate" type="button" 
                                        class="px-6 py-2 bg-gray-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel
                                    </button>
                                    <button id="confirmEvaluateBtn" type="button" 
                                        class="px-6 py-2 bg-blue-600 text-white text-base font-medium rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 flex items-center">
                                        <svg id="confirmIcon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <div id="loadingSpinner" class="w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin hidden"></div>
                                        <span id="confirmText">Confirm Evaluate</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Modal --}}
                    <div id="actionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden h-full w-full z-50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
                        <div class="w-[640px] max-w-2xl shadow-2xl rounded-2xl bg-white dark:bg-gray-800 transform transition-all duration-300 mx-auto my-auto p-0 flex flex-col justify-center items-center">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-8 pt-8 pb-6 rounded-t-2xl w-full flex items-center justify-between">
                                <h3 id="modalTitle" class="text-2xl font-bold text-white flex items-center">
                                    <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.906-1.455L3 21l2.455-5.094A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                                    </svg>
                                </h3>
                            </div>
                            <div class="p-8 w-full">
                            <form id="actionForm" method="POST" class="space-y-8 mt-2">
                                    @csrf
                                    
                                    {{-- Two Column Layout for Landscape Modal --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                                        {{-- Left Column: User Info and Comments --}}
                                        <div class="space-y-6 flex-1">
                                            <div class="mb-2">
                                                <label for="name" class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Full Name
                                                </label>
                                                <input type="text" 
                                                    id="name" 
                                                    name="name" 
                                                    class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase bg-gray-50 dark:bg-gray-700 dark:text-white font-medium text-base"
                                                    style="text-transform: uppercase;"
                                                    value="{{ Auth::user()->employeeInfo->FirstName }} {{ Auth::user()->employeeInfo->LastName }}"
                                                    readonly
                                                    required>
                                            </div>

                                            {{-- Comments Section --}}
                                            <div>
                                                <div class="flex justify-between items-center mb-3">
                                                    <label for="comments" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
                                                        </svg>
                                                        Your Feedback <span id="commentRequired" class="text-red-500 hidden">*</span>
                                                    </label>
                                                    <span id="commentError" class="text-sm text-red-500 hidden bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-md">Comments are required</span>
                                                </div>
                                                <textarea id="comments" 
                                                    name="comments" 
                                                    rows="2" 
                                                    placeholder="Please provide your detailed feedback here..."
                                                    class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 text-base"></textarea>
                                                <div class="flex justify-between items-center mt-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Be specific and constructive in your feedback</span>
                                                    <span id="charCount" class="text-xs text-gray-400 dark:text-gray-500">0 characters</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Column: Signature Selection --}}
                                        <div id="signatureSection" class="space-y-4">
                                            <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-0">
                                                Choose your signature method
                                            </label>
                                            
                                            <!-- Signature Method Tabs -->
                                            <div class="border-b border-gray-200 dark:border-gray-600">
                                                <nav class="-mb-px flex space-x-2 justify-center" aria-label="Tabs">
                                                    <button type="button" id="textSignatureTabShow" 
                                                        class="signature-tab-show whitespace-nowrap py-2 px-6 border-2 border-blue-500 font-medium text-base rounded-full text-blue-600 bg-white focus:outline-none transition-all duration-200 shadow-sm"
                                                        onclick="showSignatureTab('text')">
                                                        Text Style
                                                    </button>
                                                    <button type="button" id="drawSignatureTabShow" 
                                                        class="signature-tab-show whitespace-nowrap py-2 px-6 border-2 border-transparent font-medium text-base rounded-full text-gray-500 bg-gray-100 focus:outline-none transition-all duration-200 shadow-sm"
                                                        onclick="showSignatureTab('draw')">
                                                        Draw Signature
                                                    </button>
                                                </nav>
                                            </div>

                                            <!-- Text Style Signature Method -->
                                            <div id="textSignatureMethodShow">
                                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4">
                                                    <div id="signatureStyles" class="grid grid-cols-1 gap-3 max-h-60 overflow-y-auto pr-2">
                                                        {{-- Signature styles will be loaded here --}}
                                                    </div>
                                                    <style>
                                                        /* Import Google Fonts for signature styles */
                                                        @import url('https://fonts.googleapis.com/css2?family=Mr+Dafoe&family=Homemade+Apple&family=Pacifico&family=Dancing+Script:wght@400;500;600;700&display=swap');
                                                        
                                                        #signatureStyles {
                                                            /* Custom scrollbar */
                                                            scrollbar-width: thin;
                                                            scrollbar-color: #cbd5e1 #f1f5f9;
                                                        }
                                                        
                                                        #signatureStyles::-webkit-scrollbar {
                                                            width: 6px;
                                                        }
                                                        
                                                        #signatureStyles::-webkit-scrollbar-track {
                                                            background: #f1f5f9;
                                                            border-radius: 3px;
                                                        }
                                                        
                                                        #signatureStyles::-webkit-scrollbar-thumb {
                                                            background: #cbd5e1;
                                                            border-radius: 3px;
                                                        }
                                                        
                                                        #signatureStyles::-webkit-scrollbar-thumb:hover {
                                                            background: #94a3b8;
                                                        }
                                                        
                                                        #signatureStyles .signature-preview {
                                                            width: 100%;
                                                            min-height: 60px;
                                                            display: flex;
                                                            align-items: center;
                                                            justify-content: center;
                                                            padding: 1rem;
                                                            border-radius: 8px;
                                                            border: 2px solid #e5e7eb;
                                                            background: #f9fafb;
                                                            color: #1f2937;
                                                            cursor: pointer;
                                                            transition: all 0.2s ease;
                                                            margin-bottom: 8px;
                                                            text-align: center;
                                                            font-size: 1.25rem;
                                                            position: relative;
                                                        }
                                                        
                                                        #signatureStyles .signature-preview .font-name {
                                                            position: absolute;
                                                            top: 4px;
                                                            right: 8px;
                                                            font-size: 0.75rem;
                                                            color: #6b7280;
                                                            background: rgba(255,255,255,0.8);
                                                            padding: 2px 6px;
                                                            border-radius: 4px;
                                                        }
                                                        
                                                        #signatureStyles .signature-preview:hover {
                                                            border-color: #3b82f6;
                                                            background: #eff6ff;
                                                            transform: translateY(-1px);
                                                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                                        }
                                                        #signatureStyles .signature-preview.selected {
                                                            border-color: #3b82f6;
                                                            background: #dbeafe;
                                                            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
                                                        }
                                                    </style>
                                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                                                        Select a style and your name will be converted to a signature (scroll for more options)
                                                    </div>
                                                    <span id="signatureErrorShow" class="hidden text-xs text-red-500 block text-center mt-2">
                                                        Please select a signature style
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Drawing Signature Method -->
                                            <div id="drawSignatureMethodShow" class="hidden">
                                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-2">
                                                    <div class="text-center">
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Draw your signature in the canvas below</p>
                                                        <canvas id="signatureCanvasShow" 
                                                            width="200" 
                                                            height="48" 
                                                            class="border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-50 mx-auto cursor-crosshair shadow-lg"
                                                            style="touch-action: none;">
                                                            Your browser does not support canvas
                                                        </canvas>
                                                        <div class="mt-1 flex justify-center space-x-1">
                                                            <button type="button" onclick="clearCanvasShow()" class="px-6 py-2 text-base bg-gray-500 text-white rounded-full hover:bg-gray-600 transition-colors">Clear</button>
                                                            <button type="button" onclick="undoCanvasShow()" class="px-6 py-2 text-base bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors">Undo</button>
                                                        </div>
                                                        <span id="canvasErrorShow" class="hidden text-sm text-red-500 block text-center mt-2">
                                                            Please draw your signature
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="signature" name="signature">
                                    <input type="hidden" id="signatureStyle" name="signatureStyle">

                                    {{-- Action Buttons --}}
                                    <div class="flex justify-end space-x-3 mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <button type="button" 
                                            onclick="closeModal()"
                                            class="px-6 py-2 text-base font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-full transition-all duration-200">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                            id="submitActionBtn"
                                            class="px-6 py-2 text-base font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 dark:bg-gradient-to-r dark:from-blue-700 dark:to-purple-900 hover:from-blue-600 hover:to-purple-700 rounded-full transition-all duration-200"
                                            style="background: linear-gradient(to right, #3b82f6, #8b5cf6);"
                                        >
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            <span id="submitActionText">Submit</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Feedback Confirmation Modal --}}
                    <div id="feedbackConfirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 hidden overflow-y-auto h-full w-full z-70 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="w-[450px] max-w-lg shadow-2xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden transform transition-all duration-300">
                            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                                <h3 class="text-xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Confirm Your Feedback
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 p-4 rounded-r-lg">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-amber-800 dark:text-amber-200 font-medium">
                                                    Please review your feedback before submitting
                                                </p>
                                                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                                                    Once submitted, this feedback will be sent to the request originator
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
                                        </svg>
                                        Your Feedback:
                                    </label>
                                    <textarea id="feedbackInput" class="w-full px-2 py-1 border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 text-sm mt-2 mb-2" placeholder="Please provide your detailed feedback here..." rows="4"></textarea>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span id="confirmCharCount" class="text-xs text-gray-500 dark:text-gray-400"></span>
                                        <span class="text-xs text-green-600 dark:text-green-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Ready to submit
                                        </span>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <button type="button" 
                                        onclick="closeFeedbackConfirmModal()"
                                        class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-all duration-200">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                                        </svg>
                                        Go Back & Edit
                                    </button>
                                    <form id="feedbackForm" method="POST" action="{{ route('approvals.send_feedback', $formRequest->form_id) }}">
                                        @csrf
                                        <input type="hidden" name="comments" id="feedbackFormComments">
                                        <button type="submit"
                                            id="submitFeedbackBtn"
                                            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg transition-all duration-200">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Yes, Submit Feedback
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Short Feedback Warning Modal -->
                    <div id="shortFeedbackWarningModal" class="fixed inset-0 bg-gray-900 bg-opacity-30 hidden overflow-y-auto h-full w-full z-70 backdrop-blur-sm flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 relative">
                            <!-- Modal Header -->
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 px-6 py-4 rounded-t-xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </div>
                                        <h3 class="ml-3 text-lg font-semibold text-white">Short Feedback Warning</h3>
                                    </div>
                                    <button type="button" onclick="closeShortFeedbackWarning()" class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Feedback Seems Brief</h4>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                                            Your feedback is quite short (<span id="shortFeedbackCharCount" class="font-semibold text-yellow-600"></span> characters). 
                                            Consider adding more details to help the requester understand your perspective better.
                                        </p>
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                <strong>Tip:</strong> Good feedback includes specific points about what needs attention or improvement.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600 mt-6">
                                    <button type="button" 
                                        onclick="closeShortFeedbackWarning()"
                                        class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-all duration-200 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                                        </svg>
                                        Go Back & Add More Details
                                    </button>
                                    <button type="button" 
                                        onclick="proceedWithShortFeedback()"
                                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 rounded-lg transition-all duration-200 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Submit Anyway
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('approvals.index') }}" 
                            class="inline-flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg transition-all duration-200 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-300 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Approvals List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<form id="evaluateForm" method="POST" action="{{ route('approvals.evaluate', $formRequest->form_id) }}" style="display:none;">
    @csrf
</form>
<script>
function confirmEvaluate() {
    console.log('DEBUG: confirmEvaluate called');
    const evaluateModal = document.getElementById('evaluateConfirmModal');
    if (evaluateModal) {
        evaluateModal.classList.remove('hidden');
    } else {
        console.error('DEBUG: evaluateConfirmModal not found in DOM');
    }
    // Attach event listeners to buttons
    const confirmBtn = document.getElementById('confirmEvaluateBtn');
    const cancelBtn = document.getElementById('cancelEvaluate');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            console.log('DEBUG: Confirm Evaluate button clicked');
            document.getElementById('evaluateForm').submit();
        };
    } else {
        console.error('DEBUG: confirmEvaluateBtn not found');
    }
    if (cancelBtn) {
        cancelBtn.onclick = function() {
            console.log('DEBUG: Cancel Evaluate button clicked');
            evaluateModal.classList.add('hidden');
        };
    } else {
        console.error('DEBUG: cancelEvaluateBtn not found');
    }
}
// Signature Modal Logic
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const textTab = document.getElementById('textSignatureTabShow');
    const drawTab = document.getElementById('drawSignatureTabShow');
    const textMethod = document.getElementById('textSignatureMethodShow');
    const drawMethod = document.getElementById('drawSignatureMethodShow');
    if (textTab && drawTab && textMethod && drawMethod) {
        textTab.onclick = function() {
            textTab.classList.add('border-blue-500', 'text-blue-600', 'active');
            drawTab.classList.remove('border-blue-500', 'text-blue-600', 'active');
            textMethod.classList.remove('hidden');
            drawMethod.classList.add('hidden');
        };
        drawTab.onclick = function() {
            drawTab.classList.add('border-blue-500', 'text-blue-600', 'active');
            textTab.classList.remove('border-blue-500', 'text-blue-600', 'active');
            drawMethod.classList.remove('hidden');
            textMethod.classList.add('hidden');
        };
    }

    // Canvas drawing logic
    const canvas = document.getElementById('signatureCanvasShow');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;
        window.points = [];
        canvas.onmousedown = function(e) {
            drawing = true;
            window.points.push([]);
            ctx.beginPath();
        };
        canvas.onmouseup = function(e) {
            drawing = false;
        };
        canvas.onmouseleave = function(e) {
            drawing = false;
        };
        canvas.onmousemove = function(e) {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            if (!window.points.length) window.points.push([]);
            window.points[window.points.length-1].push([x, y]);
            if (window.points[window.points.length-1].length === 1) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
                ctx.stroke();
            }
        };
        window.clearCanvasShow = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            window.points = [];
        };
        window.undoCanvasShow = function() {
            if (window.points.length) window.points.pop();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            window.points.forEach(stroke => {
                if (stroke.length) {
                    ctx.beginPath();
                    stroke.forEach(([x, y], i) => {
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    });
                    ctx.stroke();
                }
            });
        };
    }

    // Signature style selection logic
    // Example: Load available styles (replace with dynamic if needed)
    // Get signature styles from database
    const styles = @json($signatureStyles->map(function($style) {
        return [
            'id' => $style->id,
            'name' => $style->name,
            'font_family' => $style->font_family
        ];
    }));
    
    const styleContainer = document.getElementById('signatureStyles');
    if (styleContainer) {
        styleContainer.innerHTML = '';
        styles.forEach(style => {
            const div = document.createElement('div');
            div.className = 'signature-preview';
            
            // Apply the font family with fallbacks
            const fontFamily = `"${style.font_family}", cursive, serif`;
            div.style.fontFamily = fontFamily;
            div.style.fontSize = '1.25rem';
            div.style.fontWeight = 'normal';
            
            // Apply special styling for specific fonts
            if (style.name === 'Formal Bold') {
                div.style.fontWeight = 'bold';
                div.style.textDecoration = 'underline';
            } else if (style.name === 'Typewriter') {
                div.style.fontFamily = `"${style.font_family}", monospace`;
                div.style.letterSpacing = '2px';
                div.style.fontSize = '1.1rem';
            }
            
            // Add font name badge
            const fontBadge = document.createElement('span');
            fontBadge.className = 'font-name';
            fontBadge.textContent = style.name;
            div.appendChild(fontBadge);
            
            // Add the preview text
            const previewText = document.getElementById('name') ? document.getElementById('name').value.toUpperCase() : 'SIGNATURE PREVIEW';
            div.appendChild(document.createTextNode(previewText));
            
            div.onclick = function() {
                document.getElementById('signatureStyle').value = style.id;
                // Highlight selected
                Array.from(styleContainer.children).forEach(child => child.classList.remove('selected'));
                div.classList.add('selected');
            };
            styleContainer.appendChild(div);
        });
        
        // Auto-update preview text when name changes
        const nameField = document.getElementById('name');
        if (nameField) {
            nameField.addEventListener('input', function() {
                const previews = styleContainer.querySelectorAll('.signature-preview');
                previews.forEach(preview => {
                    // Update only the text node (not the badge)
                    const textNodes = Array.from(preview.childNodes).filter(node => node.nodeType === Node.TEXT_NODE);
                    if (textNodes.length > 0) {
                        textNodes[0].textContent = (nameField.value || 'SIGNATURE PREVIEW').toUpperCase();
                    }
                });
            });
        }
    }

    // Before submit, save signature data
    const actionForm = document.getElementById('actionForm');
    const signatureError = document.getElementById('signatureErrorShow');
    const canvasError = document.getElementById('canvasErrorShow');
    if (actionForm) {
        actionForm.onsubmit = function(e) {
            let valid = false;
            // If draw tab is active, require drawn signature
            if (!drawMethod.classList.contains('hidden')) {
                if (canvas) {
                    // Check if there are any points drawn
                    if (typeof window.points !== 'undefined' && window.points.length > 0 && window.points.some(stroke => stroke.length > 0)) {
                        document.getElementById('signature').value = canvas.toDataURL();
                        valid = true;
                        canvasError.classList.add('hidden');
                    } else {
                        canvasError.classList.remove('hidden');
                    }
                }
            } else {
                // Text style: require style selection
                const styleVal = document.getElementById('signatureStyle').value;
                if (styleVal) {
                    document.getElementById('signature').value = document.getElementById('name').value;
                    valid = true;
                    signatureError.classList.add('hidden');
                } else {
                    signatureError.classList.remove('hidden');
                }
            }
            if (!valid) {
                e.preventDefault();
                return;
            }
            // AJAX submit for approval
            e.preventDefault();
            
            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('submitActionBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Processing...
            `;
            
            const formData = new FormData(actionForm);
            fetch(actionForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Handle both success and error responses
                return response.text().then(text => {
                    console.log('Raw response:', text.substring(0, 200)); // Log first 200 chars
                    
                    try {
                        const data = JSON.parse(text);
                        // Return data with response status for proper handling
                        return { data: data, ok: response.ok, status: response.status };
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
                    }
                });
            })
            .then(result => {
                const { data, ok, status } = result;
                
                if (ok && data.success) {
                    // Success case
                    // Close the modal first
                    closeModal();
                    
                    // Show success message
                    showSuccessMessage(data.message || 'Request processed successfully.');
                    
                    // Redirect to approvals index instead of reloading current page
                    setTimeout(() => {
                        window.location.href = "{{ route('approvals.index') }}";
                    }, 1500);
                } else {
                    // Error case (either !ok or !data.success)
                    // Re-enable submit button on error
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    // Check if it's a validation error (422 status) vs system error
                    if (status === 422) {
                        // Validation error - show user-friendly message
                        let msg = 'Please complete the required fields:';
                        if (data.errors && Array.isArray(data.errors)) {
                            msg += '<ul class="mt-2 list-disc list-inside text-sm">' + data.errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                        }
                        showValidationError(msg);
                    } else {
                        // System error - show as approval error
                        let msg = data.message || 'Approval failed.';
                        if (data.errors && Array.isArray(data.errors)) {
                            msg += '<ul class="mt-2 list-disc list-inside text-sm">' + data.errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                        }
                        showApprovalError(msg);
                    }
                }
            })
            .catch(err => {
                console.error('Approval error:', err);
                
                // Re-enable submit button on error
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                // Handle true network/parsing errors
                let errorMessage = 'An unexpected error occurred. Please try again.';
                if (err.message) {
                    errorMessage = err.message;
                }
                
                showApprovalError(errorMessage);
            });
        };
    }
});
</script>
<script>
function openActionModal(actionType) {
    const modal = document.getElementById('actionModal');
    const modalTitle = document.getElementById('modalTitle');
    const actionForm = document.getElementById('actionForm');
    if (!modal || !modalTitle || !actionForm) {
        console.error('DEBUG: Modal elements not found');
        return;
    }
    let titleText = '';
    let formAction = '';
    switch(actionType) {
        case 'approve':
            titleText = 'Approve Request';
            formAction = "{{ route('approvals.approve', $formRequest->form_id) }}";
            break;
        case 'reject':
            titleText = 'Reject Request';
            formAction = "{{ route('approvals.reject', $formRequest->form_id) }}";
            break;
        case 'send_feedback':
            titleText = 'Send Feedback';
            formAction = "{{ route('approvals.send_feedback', $formRequest->form_id) }}";
            break;
        default:
            titleText = 'Take Action';
            formAction = '#';
    }
    modalTitle.textContent = titleText;
    actionForm.action = formAction;
    modal.classList.remove('hidden');
    // Add close logic if needed
    const closeBtns = modal.querySelectorAll('[data-close-modal]');
    closeBtns.forEach(btn => {
        btn.onclick = function() {
            modal.classList.add('hidden');
        };
    });
}
</script>