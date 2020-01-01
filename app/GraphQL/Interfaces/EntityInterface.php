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
            ]
        ];
    }
}