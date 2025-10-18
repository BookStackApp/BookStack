<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityDefaultTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<Page> $pages
 * @property ?int             $default_template_id
 * @property string           $description
 * @property string           $description_html
 */
class Chapter extends BookChild implements HasDescriptionInterface, HasDefaultTemplateInterface
{
    use HasFactory;
    use ContainerTrait;

    public float $searchFactor = 1.2;
    protected $hidden = ['pivot', 'deleted_at', 'description_html', 'sort_rule_id', 'image_id', 'entity_id', 'entity_type', 'chapter_id'];
    protected $fillable = ['name', 'priority'];

    /**
     * Get the pages that this chapter contains.
     *
     * @return HasMany<Page, $this>
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
     * Get the visible pages in this chapter.
     * @return Collection<Page>
     */
    public function getVisiblePages(): Collection
    {
        return $this->pages()
        ->scopes('visible')
        ->orderBy('draft', 'desc')
        ->orderBy('priority', 'asc')
        ->get();
    }

    public function defaultTemplate(): EntityDefaultTemplate
    {
        return new EntityDefaultTemplate($this);
    }
}
