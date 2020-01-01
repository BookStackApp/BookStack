<?php namespace BookStack\GraphQL\Types;

use GraphQL;
use BookStack\Actions\Activity;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActivityType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Activity',
        'description'   => 'BookStack Activity',
        'model'         => Activity::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'key' => [
                'type' => Type::string(),
            ],
            'user' => [
                'type' => GraphQL::type('user'),
            ],
            'extra' => [
                'type' => Type::string(),
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
