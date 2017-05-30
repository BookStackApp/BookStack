<?php

namespace BookStack;

class Comment extends Ownable
{
    public $sub_comments = [];
    protected $fillable = ['text', 'html', 'parent_id'];
    protected $appends = ['created', 'updated', 'sub_comments'];
    /**
     * Get the entity that this comment belongs to
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }

    /**
     * Get the page that this comment is in.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the owner of this comment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPageComments($pageId) {
        $query = static::newQuery();
        $query->join('users AS u', 'comments.created_by', '=', 'u.id');
        $query->leftJoin('users AS u1', 'comments.updated_by', '=', 'u1.id');
        $query->leftJoin('images AS i', 'i.id', '=', 'u.image_id');
        $query->selectRaw('comments.id, text, html, comments.created_by, comments.updated_by, '
                . 'comments.created_at, comments.updated_at, comments.parent_id, '
                . 'u.name AS created_by_name, u1.name AS updated_by_name, '
                . 'i.url AS avatar ');
        $query->whereRaw('page_id = ?', [$pageId]);
        $query->orderBy('created_at');
        return $query->get();
    }

    public function getAllPageComments($pageId) {
        return self::where('page_id', '=', $pageId)->with(['createdBy' => function($query) {
            $query->select('id', 'name', 'image_id');
        }, 'updatedBy' => function($query) {
            $query->select('id', 'name');
        }, 'createdBy.avatar' => function ($query) {
            $query->select('id', 'path', 'url');
        }])->get();
    }

    public function getCommentById($commentId) {
        return self::where('id', '=', $commentId)->with(['createdBy' => function($query) {
            $query->select('id', 'name', 'image_id');
        }, 'updatedBy' => function($query) {
            $query->select('id', 'name');
        }, 'createdBy.avatar' => function ($query) {
            $query->select('id', 'path', 'url');
        }])->first();
    }

    public function getCreatedAttribute() {
        $created = [
            'day_time_str' => $this->created_at->toDayDateTimeString(),
            'diff' => $this->created_at->diffForHumans()
        ];
        return $created;
    }

    public function getUpdatedAttribute() {
        if (empty($this->updated_at)) {
            return null;
        }
        $updated = [
            'day_time_str' => $this->updated_at->toDayDateTimeString(),
            'diff' => $this->updated_at->diffForHumans()
        ];
        return $updated;
    }

    public function getSubCommentsAttribute() {
        return $this->sub_comments;
    }
}
