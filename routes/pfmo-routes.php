<?php

/**
 * PFMO (Physical Facilities Management Office) Routes
 * Specialized routes for facility request management
 */

use App\Http\Controllers\PFMOController;
use Illuminate\Support\Facades\Route;

// PFMO Dashboard and Management Routes
Route::middleware(['auth'])->prefix('pfmo')->name('pfmo.')->group(function () {

    // PFMO Dashboard route removed - feedback moved to main dashboard

    // Facility Requests Management
    Route::get('/facility-requests', [PFMOController::class, 'facilityRequests'])
        ->name('facility-requests')
        ->middleware('can:access-pfmo');

    // Individual Request Details
    Route::get('/request/{id}', [PFMOController::class, 'showRequest'])
        ->name('request.show')
        ->middleware('can:access-pfmo');

    // Process Approval/Denial
    Route::post('/request/{id}/process', [PFMOController::class, 'processApproval'])
        ->name('request.process')
        ->middleware('can:approve-pfmo-requests');

    // Bulk Actions
    Route::post('/bulk-action', [PFMOController::class, 'bulkAction'])
        ->name('bulk-action')
        ->middleware('can:approve-pfmo-requests');

    // Performance Metrics
    Route::get('/metrics', [PFMOController::class, 'metrics'])
        ->name('metrics')
        ->middleware('can:access-pfmo');

});

// PFMO API Routes for AJAX calls
Route::middleware(['auth', 'api'])->prefix('api/pfmo')->name('api.pfmo.')->group(function () {

    // Get dashboard data
    Route::get('/dashboard-data', function () {
        return response()->json(\App\Services\PFMOWorkflowService::getPFMODashboard());
    })->name('dashboard.data')->middleware('can:access-pfmo');

    // Get recommendations
    Route::get('/recommendations', function () {
        return response()->json(\App\Services\PFMOWorkflowService::getPFMORecommendations());
    })->name('recommendations')->middleware('can:access-pfmo');

    // Auto-categorize request
    Route::post('/categorize-request', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'description' => 'required|string',
            'title' => 'nullable|string'
        ]);

        $suggestions = \App\Services\PFMOWorkflowService::categorizePFMORequest(
            $request->description,
            $request->title ?? ''
        );

        return response()->json(['suggestions' => $suggestions]);
    })->name('categorize')->middleware('can:access-pfmo');

});
