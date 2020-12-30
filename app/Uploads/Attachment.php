<?php namespace BookStack\Uploads;

use BookStack\Entities\Models\Page;
use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;

/**
 * @property int id
 * @property string name
 * @property string path
 * @property string extension
 * @property bool external
 */
class Attachment extends Model
{
    use HasCreatorAndUpdater;

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

    /**
     * Generate a HTML link to this attachment.
     */
    public function htmlLink(): string
    {
        return '<a target="_blank" href="'.e($this->getUrl()).'">'.e($this->name).'</a>';
    }

    /**
     * Generate a markdown link to this attachment.
     */
    public function markdownLink(): string
    {
        return '['. $this->name .']('. $this->getUrl() .')';
    }
}
