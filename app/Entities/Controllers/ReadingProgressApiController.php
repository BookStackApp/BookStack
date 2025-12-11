<?php

namespace BookStack\Entities\Controllers;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\ReadingProgress;
use BookStack\Exceptions\PermissionsException;
use BookStack\Http\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReadingProgressApiController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get the reading progress for a specific page.
     */
    public function getProgress(Request $request, string $pageId): JsonResponse
    {
        $page = Page::visible()->findOrFail($pageId);
        $user = $request->user();

        $progress = ReadingProgress::forUserAndPage($user->id, $page->id);

        if (!$progress) {
            return response()->json([
                'page_id' => (int) $pageId,
                'progress_percentage' => 0,
                'scroll_position' => 0,
                'time_spent_seconds' => 0,
                'is_completed' => false,
                'last_read_at' => null,
            ]);
        }

        return response()->json($progress);
    }

    /**
     * Update or create reading progress for a page.
     */
    public function updateProgress(Request $request, string $pageId): JsonResponse
    {
        $page = Page::visible()->findOrFail($pageId);
        $user = $request->user();

        try {
            $validated = $this->validate($request, [
                'progress_percentage' => 'required|numeric|min:0|max:100',
                'scroll_position' => 'required|integer|min:0',
                'time_spent_seconds' => 'required|integer|min:0',
                'is_completed' => 'required|boolean',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid data provided', 'details' => $e->errors()], 422);
        }

        $progress = ReadingProgress::updateOrCreateProgress(
            $user->id,
            $page->id,
            $validated
        );

        // Log activity
        if ($validated['is_completed']) {
            $this->logActivity(ActivityType::PAGE_READ, $page);
        }

        return response()->json($progress, 200);
    }

    /**
     * Get reading statistics for the authenticated user.
     */
    public function getUserStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = ReadingProgress::getUserReadingStats($user->id);

        return response()->json([
            'user_id' => $user->id,
            'statistics' => $stats,
        ]);
    }

    /**
     * Get all reading progress for the authenticated user.
     */
    public function getUserProgress(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = min($request->get('limit', 50), 100);

        $progress = ReadingProgress::with('page.book')
            ->where('user_id', $user->id)
            ->orderBy('last_read_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'page_id' => $item->page_id,
                    'page_name' => $item->page->name,
                    'book_id' => $item->page->book_id,
                    'book_name' => $item->page->book->name,
                    'progress_percentage' => $item->progress_percentage,
                    'is_completed' => $item->is_completed,
                    'last_read_at' => $item->last_read_at->toISOString(),
                ];
            });

        return response()->json(['data' => $progress]);
    }

    /**
     * Delete reading progress for a specific page.
     */
    public function deleteProgress(Request $request, string $pageId): JsonResponse
    {
        $page = Page::visible()->findOrFail($pageId);
        $user = $request->user();

        $progress = ReadingProgress::forUserAndPage($user->id, $page->id);

        if ($progress) {
            $progress->delete();
        }

        return response()->json(['message' => 'Reading progress deleted successfully'], 200);
    }
}