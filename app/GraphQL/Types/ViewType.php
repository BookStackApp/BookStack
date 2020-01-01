<?php namespace BookStack\GraphQL\Types;

use BookStack\Actions\View;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ViewType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'View',
        'description'   => 'BookStack View',
        'model'         => View::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'views' => [
                'type' => Type::int(),
            ],
            'createdAt' => [
                'type' => Type::string(),
                'alias' => 'created_at',
            ],
            'updatedAt' => [
                'type' => Type::string(),
                'alias' => 'updated_at',
            ]
        ];
    }
}
