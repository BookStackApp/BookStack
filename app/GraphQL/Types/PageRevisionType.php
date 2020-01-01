<?php namespace BookStack\GraphQL\Types;

use BookStack\Entities\PageRevision;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageRevisionType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'PageRevision',
        'model'         => PageRevision::class,
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
            'bookSlug' => [
                'type' => Type::string(),
                'alias' => 'book_slug',
            ],
            'createdBy' => [
                'type' => GraphQL::type('user'),
            ],
            'createdAt' => [
                'type' => Type::string(),
                'alias' => 'created_at',
            ],
            'updatedAt' => [
                'type' => Type::string(),
                'alias' => 'updated_at',
            ],
            'type' => [
                'type' => Type::string(),
            ],
            'summary' => [
                'type' => Type::string(),
            ],
            'markdown' => [
                'type' => Type::string(),
            ],
            'html' => [
                'type' => Type::string(),
            ],
            'revisionNumber' => [
                'type' => Type::int(),
                'alias' => 'revision_number',
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'text' => [
                'type' => Type::string(),
            ],
            'page' => [
                'type' => GraphQL::type('page'),
            ]
        ];
    }
}
