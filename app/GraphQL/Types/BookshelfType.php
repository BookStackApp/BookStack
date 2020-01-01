<?php namespace BookStack\GraphQL\Types;

use BookStack\Entities\Bookshelf;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BookshelfType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Bookshelf',
        'description'   => 'BookStack Bookshelf',
        'model'         => Bookshelf::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'slug' => [
                'type' => Type::string(),
            ],
            'description' => [
                'type' => Type::string(),
            ],
            'createdAt' => [
                'type' => Type::string(),
                'alias' => 'created_at',
            ],
            'updatedAt' => [
                'type' => Type::string(),
                'alias' => 'updated_at',
            ],
            'restricted' => [
                'type' => Type::int(),
            ],
            'createdBy' => [
                'type' => Type::getNullableType(GraphQL::type('user')),
            ],
            'updatedBy' => [
                'type' => Type::getNullableType(GraphQL::type('user')),
            ],
            'activity' => [
                'type' => Type::listOf(GraphQL::type('activity')),
                'description' => 'Get activities of Entity.'
            ],
            'views' => [
                'type' => Type::listOf(GraphQL::type('view')),
            ],
            'tags' => [
                'type' => Type::listOf(GraphQL::type('tag')),
            ],
            'comments' => [
                'type' => Type::listOf(GraphQL::type('comment')),
            ],
            'searchTerms' => [
                'type' => Type::listOf(GraphQL::type('searchTerm')),
            ],
            'books' => [
                'type' => Type::listOf(GraphQL::type('book')),
            ]
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('entity'),
            GraphQL::type('ownable'),
        ];
    }
}
