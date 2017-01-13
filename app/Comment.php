<?php

namespace BookStack;

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
}
