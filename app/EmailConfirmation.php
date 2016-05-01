<?php namespace BookStack;

class EmailConfirmation extends Model
{
    protected $fillable = ['user_id', 'token'];

    /**
     * Get the user that this confirmation is attached to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
