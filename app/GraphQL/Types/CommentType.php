<?php namespace BookStack\GraphQL\Types;

use GraphQL;
use BookStack\Actions\Comment;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CommentType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Comment',
        'description'   => 'BookStack Comment',
        'model'         => Comment::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'text' => [
                'type' => Type::string(),
            ],
            'html' => [
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
            'createdBy' => [
                'type' => Type::getNullableType(GraphQL::type('user')),
            ],
            'updatedBy' => [
                'type' => Type::getNullableType(GraphQL::type('user')),
            ],
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('ownable')
        ];
    }
}
