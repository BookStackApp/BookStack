<?php namespace BookStack;

class PageRevision extends Model
{
    protected $fillable = ['name', 'html', 'text', 'markdown', 'summary'];

    /**
     * Get the user that created the page revision
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page this revision originates from.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the url for this revision.
     * @param null|string $path
     * @return string
     */
    public function getUrl($path = null)
    {
        $url = $this->page->getUrl() . '/revisions/' . $this->id;
        if ($path) {
            return $url . '/' . trim($path, '/');
        }
        return $url;
    }

    /**
     * Get the previous revision for the same page if existing
     * @return \BookStack\PageRevision|null
     */
    public function getPrevious()
    {
        if ($id = static::where('page_id', '=', $this->page_id)->where('id', '<', $this->id)->max('id')) {
            return static::find($id);
        }
        return null;
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Included here to align with entities in similar use cases.
     * (Yup, Bit of an awkward hack)
     * @param $type
     * @return bool
     */
    public static function isA($type)
    {
        return $type === 'revision';
    }
}
