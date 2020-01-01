<?php namespace BookStack\GraphQL\Queries;

use BookStack\Entities\Bookshelf;
use Closure;
use GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use BookStack\Entities\Book;

class BookshelvesQuery extends Query
{
    protected $attributes = [
        'name' => 'Bookshelves query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('bookshelf'));
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'slug' => ['name' => 'slug', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['id'])) {
            return Bookshelf::where('id', $args['id'])->get();
        }

        if (isset($args['slug'])) {
            return Bookshelf::where('slug', $args['slug'])->get();
        }

        return Bookshelf::all();
    }
}
