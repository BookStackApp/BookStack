<?php namespace BookStack\GraphQL\Interfaces;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InterfaceType;

class BookChildInterface extends InterfaceType
{
    protected $attributes = [
        'name' => 'BookChild',
        'description' => 'BookChild interface.',
    ];

    public function fields(): array
    {
        return [
            'book' => [
                'type' => GraphQL::type('book'),
            ],
        ];
    }
}