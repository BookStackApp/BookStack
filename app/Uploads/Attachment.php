<?php namespace BookStack\Uploads;

use BookStack\Entities\Page;
use BookStack\Ownable;

class Attachment extends Ownable
{
    protected $fillable = ['name', 'order'];

    /**
     * Get the downloadable file name for this upload.
     * @return mixed|string
     */
    public function getFileName()
    {
        if (strpos($this->name, '.') !== false) {
            return $this->name;
        }
        return $this->name . '.' . $this->extension;
    }

    /**
     * Get the page this file was uploaded to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'uploaded_to');
    }

    /**
     * Get the url of this file.
     */
    public function getUrl(): string
    {
        if ($this->external && strpos($this->path, 'http') !== 0) {
            return $this->path;
        }
        return url('/attachments/' . $this->id);
    }
}
