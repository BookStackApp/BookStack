<?php namespace BookStack\GraphQL\Types;

use BookStack\Entities\Chapter;
use GraphQL;
use BookStack\Actions\Activity;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ChapterType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Chapter',
        'description'   => 'BookStack Chapter',
        'model'         => Chapter::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'slug' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'description' => [
                'type' => Type::string(),
            ],
            'createdAt' => [
                'type' => Type::string(),
                'alias' => 'created_at',
            ],
            'priority' => [
                'type' => Type::int(),
            ],
            'updatedAt' => [
                'type' => Type::string(),
                'alias' => 'updated_at',
            ],
            'book' => [
                'type' => GraphQL::type('book'),
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
            ]
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('bookChild'),
            GraphQL::type('entity'),
            GraphQL::type('ownable'),
        ];
    }
}
