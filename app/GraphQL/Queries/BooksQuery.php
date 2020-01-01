<?php namespace BookStack\GraphQL\Queries;

use Closure;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use BookStack\Entities\Book;

class BooksQuery extends Query
{
    protected $attributes = [
        'name' => 'Books query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('book'));
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
            return Book::where('id', $args['id'])->get();
        }

        if (isset($args['slug'])) {
            return Book::where('slug', $args['slug'])->get();
        }

        return Book::all();
    }
}
