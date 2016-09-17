<?php namespace BookStack;

use Illuminate\Notifications\Notifiable;

class EmailConfirmation extends Model
{
    use Notifiable;

    protected $fillable = ['user_id', 'token'];

    /**
     * Get the user that this confirmation is attached to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the routing for mail notifications.
     * @return mixed
     */
    public function routeNotificationForMail()
    {
        return $this->user->email;
    }
    
}
