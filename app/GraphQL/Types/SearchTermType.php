<?php namespace BookStack\GraphQL\Types;

use BookStack\Actions\Activity;
use BookStack\Entities\SearchTerm;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SearchTermType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'SearchTerm',
        'description'   => 'BookStack SearchTerm',
        'model'         => SearchTerm::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'term' => [
                'type' => Type::string(),
            ],
            'score' => [
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
