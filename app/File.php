<?php namespace BookStack;


class File extends Ownable
{
    protected $fillable = ['name', 'order'];

    /**
     * Get the page this file was uploaded to.
     * @return mixed
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'uploaded_to');
    }


}
