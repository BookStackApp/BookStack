<?php namespace BookStack;

class Attachment extends Ownable
{
    protected $fillable = ['name', 'order'];

    /**
     * Get the downloadable file name for this upload.
     * @return mixed|string
     */
    public function getFileName()
    {
        if (str_contains($this->name, '.')) {
            return $this->name;
        }
        return $this->name . '.' . $this->extension;
    }

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
        if ($this->external && strpos($this->path, 'http') !== 0) {
            return $this->path;
        }
        return baseUrl('/attachments/' . $this->id);
    }
}
