<?php namespace BookStack\GraphQL\Types;

use BookStack\Uploads\Image;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ImageType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Image',
        'description'   => 'BookStack Image',
        'model'         => Image::class,
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
            'url' => [
                'type' => Type::string(),
            ],
            'path' => [
                'type' => Type::string(),
            ],
            'type' => [
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
            'page' => [
                'type' => Type::getNullableType(GraphQL::type('Page')),
                'resolve' => function ($root, $args) {
                    return $root->getPage();
                }
            ],
            'thumb' => [
                'type' => Type::string(),
                'args' => [
                    'width' => [
                        'type' => Type::int(),
                        'defaultValue' => 220,
                    ],
                    'height' => [
                        'type' => Type::int(),
                        'defaultValue' => 220
                    ],
                    'keepRatio' => [
                        'type' => Type::boolean(),
                        'defaultValue' => false
                    ]
                ],
                'resolve' => function ($root, $args) {
                    return $root->getThumb($args["width"], $args["height"], $args["keepRatio"]);
                }
            ],
            'createdBy' => [
                'type' => Type::getNullableType(GraphQL::type('User')),
            ],
            'updatedBy' => [
                'type' => Type::getNullableType(GraphQL::type('User')),
            ],
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('Ownable'),
        ];
    }
}
