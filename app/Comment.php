<?php

namespace BookStack;
use Illuminate\Support\Facades\DB;

class Comment extends Ownable
{
    protected $fillable = ['text', 'html'];
    
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
    
    public function getCommentsByPage($pageId, $commentId, $pageNum = 0, $limit = 0) {        
  
        $query = static::newQuery();
        $query->join('users AS u', 'comments.created_by', '=', 'u.id');
        $query->leftJoin('users AS u1', 'comments.updated_by', '=', 'u1.id');
        $query->leftJoin('images AS i', 'i.id', '=', 'u.image_id');
        $query->selectRaw('comments.id, text, html, comments.created_by, comments.updated_by, comments.created_at, comments.updated_at, '
                . 'u.name AS created_by_name, u1.name AS updated_by_name, '
                . '(SELECT count(c.id) FROM bookstack.comments c WHERE c.parent_id = comments.id AND page_id = ?) AS cnt_sub_comments, i.url AS avatar ', 
                [$pageId]);
        
        if (empty($commentId)) {
            $query->whereRaw('page_id = ? AND parent_id IS NULL', [$pageId]);
        } else {
            $query->whereRaw('page_id = ? AND parent_id = ?', [$pageId, $commentId]);
        }        
        $query->orderBy('created_at');
        return $query;
    }
}
