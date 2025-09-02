<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ApproverAssignmentController;
use App\Http\Controllers\SignatureStyleController;
use App\Http\Controllers\AdminController; // Import AdminController
use App\Http\Controllers\Api\UserLookupController; // Add this import
use App\Http\Controllers\WorkflowPreviewController; // Add WorkflowPreviewController
use Illuminate\Support\Facades\Log;

// Include debug routes in development environment only
if (app()->environment('local')) {
    include __DIR__ . '/debug-routes.php';
    include __DIR__ . '/debug-department-routes.php';
}

// API Routes for user lookup (before authentication)
Route::get('/api/user/lookup', [UserLookupController::class, 'lookup'])->name('api.user.lookup');

// Workflow Preview API Route (before authentication for now)
Route::post('/workflow/preview', [WorkflowPreviewController::class, 'preview'])->name('workflow.preview');

Route::get('/', function () {
    if (auth()->check()) {
        // Redirect admin to admin dashboard, others to regular dashboard
        if (auth()->user()->accessRole === 'Admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Redirect /login to root URL
Route::get('/login', function () {
    return redirect('/');
})->name('login');

Route::middleware(['auth', 'prevent.admin'])->group(function () {
    // Dashboard - Employee dashboard only
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard API routes
    Route::get('/api/dashboard/more-feedback', [DashboardController::class, 'moreFeedback'])->name('dashboard.more-feedback');

    // Unified Requests - Employee only
    Route::get('/requests', [RequestController::class, 'index'])->name('request.index'); // For listing all types of requests
    Route::get('/requests/create', [RequestController::class, 'create'])->name('request.create'); // Unified create form
    Route::post('/requests/submit-for-confirmation', [RequestController::class, 'submitForConfirmation'])->name('request.submit_for_confirmation'); // New: initial submission for confirmation
    Route::get('/requests/confirmation', [RequestController::class, 'showConfirmationPage'])->name('request.show_confirmation_page'); // New: display confirmation page
    Route::get('/requests/edit-before-confirmation', [RequestController::class, 'editBeforeConfirmation'])->name('request.edit_before_confirmation'); // New: for going back to edit from confirmation
    Route::post('/requests', [RequestController::class, 'store'])->name('request.store'); // Unified store action (now for final submission from confirmation)
    Route::post('/requests/auto-assign', [RequestController::class, 'autoAssign'])->name('request.auto-assign'); // AJAX endpoint for auto-assignment

    // Notification Count API for badge notifications
    Route::get('/notifications/count', [\App\Http\Controllers\NotificationController::class, 'getCount'])->name('notifications.count');

    // Job Order Routes - Employee only (PFMO staff/head)
    Route::get('/job-orders', [\App\Http\Controllers\JobOrderController::class, 'index'])->name('job-orders.index');
    Route::get('/job-orders/{jobOrder}', [\App\Http\Controllers\JobOrderController::class, 'show'])->name('job-orders.show');
    Route::post('/job-orders/{jobOrder}/start', [\App\Http\Controllers\JobOrderController::class, 'startJob'])->name('job-orders.start');
    Route::post('/job-orders/{jobOrder}/progress', [\App\Http\Controllers\JobOrderController::class, 'updateProgress'])->name('job-orders.progress');
    Route::post('/job-orders/{jobOrder}/complete', [\App\Http\Controllers\JobOrderController::class, 'completeJob'])->name('job-orders.complete');
    Route::post('/job-orders/{jobOrder}/submit-complete', [\App\Http\Controllers\JobOrderController::class, 'submitCompleteForm'])->name('job-orders.submit-complete');
    Route::get('/job-orders/{jobOrder}/printable-form', [\App\Http\Controllers\JobOrderController::class, 'printableForm'])->name('job-orders.printable-form');

    // Job Order Requestor Routes (for requestors to fill-up and provide feedback)
    Route::post('/job-order/{jobOrder}/fill-up', [\App\Http\Controllers\JobOrderController::class, 'fillUpJobOrder'])->name('job-order.fill-up');
    Route::post('/job-order/{jobOrder}/feedback', [\App\Http\Controllers\JobOrderController::class, 'submitFeedback'])->name('job-order.feedback');

    // Job Order Analytics Routes (PFMO Head only)
    Route::get('/job-orders/analytics/dashboard', [\App\Http\Controllers\JobOrderAnalyticsController::class, 'index'])->name('job-orders.analytics');
    Route::get('/job-orders/analytics/api', [\App\Http\Controllers\JobOrderAnalyticsController::class, 'apiData'])->name('job-orders.analytics.api');

    // Job Order Feedback Routes
    Route::get('/job-orders/feedback/pending', [\App\Http\Controllers\JobOrderFeedbackController::class, 'pendingFeedback'])->name('job-orders.pending-feedback');
    Route::get('/job-orders/{jobOrder}/feedback', [\App\Http\Controllers\JobOrderFeedbackController::class, 'showFeedbackForm'])->name('job-orders.feedback-form');
    Route::post('/job-orders/{jobOrder}/feedback', [\App\Http\Controllers\JobOrderFeedbackController::class, 'submitFeedback'])->name('job-orders.submit-feedback');
    Route::get('/job-orders/feedback/check', [\App\Http\Controllers\JobOrderFeedbackController::class, 'checkPendingFeedback'])->name('job-orders.check-feedback');

    // Approvals Route (for users with 'Approver' accessRole) - Employee only
    Route::get('/approvals', [ApprovalController::class, 'index'])
        ->name('approvals.index')
        ->middleware('can:view-approvals'); // We will create this Gate

    Route::get('/approvals/{formRequest}/show', [ApprovalController::class, 'show'])
        ->name('approvals.show')
        ->middleware('can:view-approvals'); // Protects even seeing the detail page

    // Approval Actions - Employee only
    Route::post('/approvals/{formRequest}/approve', [ApprovalController::class, 'approve'])
        ->name('approvals.approve')
        ->middleware('can:view-approvals');

    Route::post('/approvals/{formRequest}/evaluate', [ApprovalController::class, 'evaluate'])
        ->name('approvals.evaluate')
        ->middleware('can:view-approvals');

    Route::post('/approvals/{formRequest}/send-feedback', [ApprovalController::class, 'sendFeedback'])
        ->name('approvals.send_feedback')
        ->middleware('can:view-approvals');

    Route::post('/approvals/{formRequest}/reject', [ApprovalController::class, 'reject'])
        ->name('approvals.reject')
        ->middleware('can:view-approvals');

    // Approver Assignment Routes - Employee only
    Route::get('/approver-assignments', [ApproverAssignmentController::class, 'index'])
        ->name('approver-assignments.index')
        ->middleware('can:manage-approvers');

    Route::put('/approver-assignments/{user}', [ApproverAssignmentController::class, 'update'])
        ->name('approver-assignments.update')
        ->middleware('can:manage-approvers');

    Route::get('/approver-assignments/check-updates', [ApproverAssignmentController::class, 'checkUpdates'])
        ->name('approver-assignments.check-updates')
        ->middleware('can:manage-approvers');

    Route::get('/request/{formId}/track', [RequestController::class, 'track'])
        ->name('request.track');

    // Signature Styles - Employee only
    Route::get('/signature-styles', [SignatureStyleController::class, 'index'])->name('signature-styles.index');

    // Approvals routes - Employee only
    Route::post('/approvals/batch', [ApprovalController::class, 'batch'])->name('approvals.batch');

    Route::get('/request/{formId}/print', [RequestController::class, 'printView'])->name('request.print');

    // Approvals routes - Employee only
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/check-updates', [ApprovalController::class, 'checkUpdates'])->name('approval.check-updates');
    Route::get('/approval/{formId}', [ApprovalController::class, 'view'])->name('approval.view');
});

// Profile Routes - Accessible by all authenticated users including Admin
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Note: Delete account route removed as per requirements
});

// Admin Routes
Route::middleware(['auth', 'admin', 'prevent.employee'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Add other admin routes here
    Route::get('/requests/{formId}/track', [AdminController::class, 'showRequestTrack'])->name('request.track');

    // Admin-only ETL upload (employee import) - integrated into employee list page
    // Route::get('/employee-import', [AdminController::class, 'showEmployeeImportForm'])->name('employee_import_form');
    Route::post('/employee-import', [AdminController::class, 'importEmployeesFromExcel'])->name('employee_import');
    Route::get('/employees', [AdminController::class, 'showAllEmployees'])->name('employee_list');
});

Route::get('/test-log', function () {
    Log::info('This is a test log entry from /test-log route.');
    return 'Test log entry attempted. Check laravel.log.';
});

require __DIR__ . '/auth.php';

// Include PFMO specialized routes
require __DIR__ . '/pfmo-routes.php';
