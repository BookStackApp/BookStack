<?php namespace BookStack\GraphQL\Interfaces;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InterfaceType;

class OwnableInterface extends InterfaceType
{
    protected $attributes = [
        'name' => 'Ownable',
        'description' => 'Ownable interface.',
    ];

    public function fields(): array
    {
        return [
            'createdAt' => [
                'type' => Type::getNullableType(GraphQL::type('User')),
                'description' => 'The user that created Ownable.'
            ],
            'updatedAt' => [
                'type' => Type::getNullableType(GraphQL::type('User')),
                'description' => 'The user that last updated Ownable.'
            ],
        ];
    }
}