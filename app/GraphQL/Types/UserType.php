<?php namespace BookStack\GraphQL\Types;

use \BookStack\Auth\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'User',
        'description'   => 'BookStack User',
        'model'         => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the user',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of user',
            ],
            // Uses the 'getIsMeAttribute' function on our custom User model
            'email' => [
                'type' => Type::string(),
                'description' => 'The name of email',
            ]
        ];
    }
}
