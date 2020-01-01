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
                'type' => GraphQL::type('Chapter'),
            ],
            'revisions' => [
                'type' => Type::listOf(GraphQL::type('PageRevision')),
            ],
            'attachments' => [
                'type' => Type::listOf(GraphQL::type('Attachment')),
            ],
            'currentRevision' => [
                'type' => GraphQL::type('PageRevision'),
                'resolve' => function ($root, $args) {
                    return $root->getCurrentRevision();
                }
            ],
            'parent' => [
                'type' => GraphQL::type('Entity'),
                'resolve' => function ($root, $args) {
                    $root->parent();
                }
            ],
            'book' => [
                'type' => GraphQL::type('Book'),
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
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('BookChild'),
            GraphQL::type('Entity'),
            GraphQL::type('Ownable'),
        ];
    }
}
