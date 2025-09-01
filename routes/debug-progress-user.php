<?php

use App\Models\JobOrderProgress;
use App\Models\User;

Route::get('/debug-progress-user', function() {
    try {
        // Test if we can get a JobOrderProgress record
        $progress = JobOrderProgress::first();
        
        if (!$progress) {
            return response()->json(['error' => 'No progress records found']);
        }
        
        // Test if we can access the user relationship
        $user = $progress->user;
        
        return response()->json([
            'progress_id' => $progress->progress_id,
            'user_id' => $progress->user_id,
            'user' => $user ? [
                'accnt_id' => $user->accnt_id,
                'username' => $user->username ?? 'N/A'
            ] : null,
            'relationship_works' => $user !== null
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
