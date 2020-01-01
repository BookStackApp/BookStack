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
                'type' => Type::getNullableType(GraphQL::type('User')),
            ],
            'updatedBy' => [
                'type' => Type::getNullableType(GraphQL::type('User')),
            ],
            'activity' => [
                'type' => Type::listOf(GraphQL::type('Activity')),
                'description' => 'Get activities of Entity.'
            ],
            'views' => [
                'type' => Type::listOf(GraphQL::type('View')),
            ],
            'tags' => [
                'type' => Type::listOf(GraphQL::type('Tag')),
            ],
            'comments' => [
                'type' => Type::listOf(GraphQL::type('Comment')),
            ],
            'searchTerms' => [
                'type' => Type::listOf(GraphQL::type('SearchTerm')),
            ],
            'books' => [
                'type' => Type::listOf(GraphQL::type('Book')),
            ]
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('Entity'),
            GraphQL::type('Ownable'),
        ];
    }
}
