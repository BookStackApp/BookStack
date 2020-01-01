<?php namespace BookStack\GraphQL\Types;

use BookStack\Entities\Page;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PageType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Page',
        'description'   => 'BookStack Page',
        'model'         => Page::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the book',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of book',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of book'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of book',
            ],
            'createdAt' => [
                'type' => Type::string(),
                'description' => 'Creation date of book',
                'alias' => 'created_at',
            ],
            'updatedAt' => [
                'type' => Type::string(),
                'description' => 'Update date of book',
                'alias' => 'updated_at',
            ],
            'chapter' => [
                'type' => GraphQL::type('chapter'),
            ],
            'revisions' => [
                'type' => Type::listOf(GraphQL::type('pageRevision')),
            ],
            'attachments' => [
                'type' => Type::listOf(GraphQL::type('attachment')),
            ],
            'currentRevision' => [
                'type' => GraphQL::type('pageRevision'),
                'resolve' => function ($root, $args) {
                    return $root->getCurrentRevision();
                }
            ],
            'parent' => [
                'type' => GraphQL::type('entity'),
                'resolve' => function ($root, $args) {
                    $root->parent();
                }
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
            ],
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
