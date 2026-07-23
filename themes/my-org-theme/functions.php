<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

Route::middleware(['web', 'auth'])->post('/ajax/training-complete', function (Request $request) {
$functionName = '[TrainingCompleteHandler]';

try {

    $userId = auth()->id();
        if (!$userId) {
            Log::error("{$functionName} Unauthenticated access attempt blocked.");
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Valid user session required.'
            ], 401);
        }


        try {
            $validated = $request->validate([
                'page_name' => 'required|string|max:255',
                'page_url'  => 'required|url',
                'page_id'   => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            Log::error("{$functionName} Validation failed for User ID {$userId}: " . json_encode($e->errors()));
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid input data.',
                'errors'  => $e->errors()
            ], 422);
        }

        $pageId = (int) $validated['page_id'];

        try {
            $alreadyCompleted = DB::table('page_completions')
                ->where('user_id', $userId)
                ->where('page_id', $pageId)
                ->exists();

            if ($alreadyCompleted) {
                return response()->json([
                    'status'  => 'already_completed', 
                    'message' => 'You have already completed this training!'
                ]);
            }
        } catch (Throwable $e) {
            Log::error("{$functionName} Database read query failed for User ID {$userId}, Page ID {$pageId}: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Database operation failed.'
            ], 500);
        }

        $webhookUrl = config('services.slack.webhook_url') ?: env('SLACK_WEBHOOK_URL');
        if (empty($webhookUrl)) {
            Log::error("{$functionName} Slack Webhook URL is missing from configuration or .env file.");
            return response()->json([
                'status'  => 'error', 
                'message' => 'Slack notification service is not configured.'
            ], 500);
        }

        $user = auth()->user();
        $userName = $user ? $user->name : 'Unknown User';

        $payload = [
            "text" => ":blue_book: *Training Completed!* :blue_book:\n" .
                      "*User:* " . $userName . "\n" .
                      "*Page:* <" . $validated['page_url'] . "|" . $validated['page_name'] . ">\n" .
                      "*Time:* " . now()->setTimezone('Asia/Kolkata')->format('Y-m-d h:i:s A')
        ];

        try {
            $response = Http::timeout(100)->withHeaders([
                'Content-Type' => 'application/json'
            ])->post($webhookUrl, $payload);
        } catch (Throwable $e) {
            Log::error("{$functionName} Network connection to Slack failed for User ID {$userId}: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Unable to connect to notification service.'
            ], 504);
        }

        if (!$response->successful()) {
            Log::error("{$functionName} Slack Webhook API failed with Status {$response->status()}: " . $response->body());
            return response()->json([
                'status'  => 'error', 
                'message' => 'Failed to deliver notification to Slack.'
            ], 502);
        }

        try {
            DB::table('page_completions')->insert([
                'user_id'    => $userId,
                'page_id'    => $pageId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'ok']);

        } catch (Throwable $e) {
            Log::error("{$functionName} Database insert failed for User ID {$userId}, Page ID {$pageId}: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save completion status.'
            ], 500);
        }

    } catch (Throwable $e) {    
        Log::error("{$functionName} Unexpected System Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        return response()->json([
            'status'  => 'error',
            'message' => 'An unexpected server error occurred.'
        ], 500);
}

});