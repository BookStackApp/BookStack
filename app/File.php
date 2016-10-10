<?php namespace BookStack;


class File extends Ownable
{
    protected $fillable = ['name', 'order'];

    /**
     * Get the page this file was uploaded to.
     * @return Page
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'uploaded_to');
    }

    /**
     * Get the url of this file.
     * @return string
     */
    public function getUrl()
    {
        return '/files/' . $this->id;
    }

}
