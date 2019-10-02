<?php namespace BookStack\Entities\Repos;


class PageRepo extends EntityRepo
{

    /**
     * Get pages that have been marked as templates.
     * @param int $count
     * @param int $page
     * @param string $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPageTemplates(int $count = 10, int $page = 1, string $search = '')
    {
        $query = $this->entityQuery('page')
            ->where('template', '=', true)
            ->orderBy('name', 'asc')
            ->skip(($page - 1) * $count)
            ->take($count);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $paginator = $query->paginate($count, ['*'], 'page', $page);
        $paginator->withPath('/templates');

        return $paginator;
    }
}
