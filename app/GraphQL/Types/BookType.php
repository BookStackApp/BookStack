<?php namespace BookStack\GraphQL\Types;

use GraphQL;
use BookStack\Entities\Book;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BookType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Book',
        'description'   => 'BookStack Book',
        'model'         => Book::class,
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
            'pages' => [
                'type' => Type::listOf(GraphQL::type('page')),
            ],
            'directPages' => [
                'type' => Type::listOf(GraphQL::type('page')),
            ],
            'chapters' => [
                'type' => Type::listOf(GraphQL::type('chapter')),
            ],
            'shelves' => [
                'type' => Type::listOf(GraphQL::type('bookshelf')),
            ],
            'excerpt' => [
                'type' => Type::string(),
                'args'          => [
                    'length' => [
                        'type' => Type::int(),
                        'defaultValue' => 100,
                    ],
                ],
                'resolve' => function ($root, $args) {
                    // If you want to resolve the field yourself,
                    // it can be done here
                    return $root->getExcerpt($args["length"]);
                }
            ],
            'directChildren' => [
                'type' => GraphQL::type('bookChild'),
                'resolve' => function ($root, $args) {
                    return $root->getDirectChildren()->all();
                }
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
            GraphQL::type('entity'),
            GraphQL::type('ownable'),
        ];
    }
}
