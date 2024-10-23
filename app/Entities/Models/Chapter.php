<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Chapter.
 *
 * @property Collection<Page> $pages
 * @property ?int             $default_template_id
 * @property ?Page            $defaultTemplate
 */
class Chapter extends BookChild
{
    use HasFactory;
    use HasHtmlDescription;

    public float $searchFactor = 1.2;

    protected $fillable = ['name', 'description', 'priority'];
    protected $hidden = ['pivot', 'deleted_at', 'description_html'];

    /**
     * Get the pages that this chapter contains.
     *
     * @return HasMany<Page>
     */
    public function pages(string $dir = 'ASC'): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('priority', $dir);
    }

    /**
     * Get the url of this chapter.
     */
    public function getUrl(string $path = ''): string
    {
        $parts = [
            'books',
            urlencode($this->book_slug ?? $this->book->slug),
            'chapter',
            urlencode($this->slug),
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Get the Page that is used as default template for newly created pages within this Chapter.
     */
    public function defaultTemplate(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'default_template_id');
    }

    /**
     * Get the visible pages in this chapter.
     * @returns Collection<Page>
     */
    public function getVisiblePages(): Collection
    {
        return $this->pages()
        ->scopes('visible')
        ->orderBy('draft', 'desc')
        ->orderBy('priority', 'asc')
        ->get();
    }
}
