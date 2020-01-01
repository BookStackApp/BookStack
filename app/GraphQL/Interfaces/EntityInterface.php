<?php namespace BookStack\GraphQL\Interfaces;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InterfaceType;

class EntityInterface extends InterfaceType
{
    protected $attributes = [
        'name' => 'Entity',
        'description' => 'Entity interface.',
    ];

    public function fields(): array
    {
        return [
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
}