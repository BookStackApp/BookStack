<?php namespace BookStack\GraphQL\Types;

use BookStack\Actions\Tag;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TagType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Tag',
        'description'   => 'BookStack Activity',
        'model'         => Tag::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'value' => [
                'type' => Type::string(),
            ],
            'order' => [
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
