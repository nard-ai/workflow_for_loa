<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\WorkflowPreviewService;
use Illuminate\Support\Facades\Auth;

Route::get('/test-workflow-preview', function () {
    return view('test-workflow-preview');
});

Route::post('/test-workflow-preview-api', function (Request $request) {
    try {
        // Mock a user for testing
        $mockUser = (object) [
            'accnt_id' => 'test_user',
            'position' => 'Staff',
            'department_id' => 1,
            'department' => (object) ['dept_name' => 'Test Department'],
            'employeeInfo' => (object) ['FirstName' => 'Test', 'LastName' => 'User']
        ];
        
        // Temporarily set mock user
        Auth::shouldReceive('user')->andReturn($mockUser);
        
        $testData = [
            'form_type' => 'IOM',
            'to_department_id' => '1',
            'title' => 'Test Request',
            'description' => 'Testing workflow preview functionality'
        ];
        
        $result = WorkflowPreviewService::generateWorkflowPreview($testData);
        
        return response()->json([
            'success' => true,
            'message' => 'Test completed successfully',
            'test_data' => $testData,
            'preview_result' => $result
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Test failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
