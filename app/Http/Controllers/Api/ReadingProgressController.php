<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadingProgress;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReadingProgressController extends ApiController
{
    /**
     * 获取指定页面的阅读进度
     */
    public function show(Request $request, $pageId)
    {
        $this->validate($request, [
            'page_id' => 'required|integer|exists:pages,id',
        ]);

        $progress = ReadingProgress::where('user_id', $request->user()->id)
            ->where('page_id', $pageId)
            ->first();

        return response()->json($progress ?? new ReadingProgress());
    }

    /**
     * 保存或更新阅读进度
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'page_id' => 'required|integer|exists:pages,id',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'last_read_position' => 'required|integer|min:0',
            'is_completed' => 'boolean',
        ]);

        $page = Page::findOrFail($request->page_id);

        $progress = ReadingProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'page_id' => $request->page_id,
            ],
            [
                'book_id' => $page->book_id,
                'progress_percentage' => $request->progress_percentage,
                'last_read_position' => $request->last_read_position,
                'last_read_at' => now(),
                'is_completed' => $request->boolean('is_completed', false),
                'started_reading_at' => function ($query) {
                    return $query->whereNull('started_reading_at')->get()->isNotEmpty() ? now() : null;
                },
            ]
        );

        return response()->json($progress);
    }
}