<?php namespace BookStack\GraphQL\Types;

use BookStack\Uploads\Attachment;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachmentType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Attachment',
        'description'   => 'BookStack Attachment',
        'model'         => Attachment::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'filename' => [
                'type' => Type::string(),
                'resolve' => function ($root, $args) {
                    return $root->getFilename();
                }
            ],
            'path' => [
                'type' => Type::string(),
            ],
            'url' => [
                'type' => Type::string(),
                'resolve' => function ($root, $args) {
                    return $root->getUrl();
                }
            ],
            'external' => [
                'type' => Type::int(),
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
            ],
            'createdBy' => [
                'type' => Type::getNullableType(GraphQL::type('user')),
            ],
            'updatedBy' => [
                'type' => Type::getNullableType(GraphQL::type('user')),
            ],
            'page' => [
                'type' => GraphQL::type('page'),
            ],
        ];
    }

    public function interfaces(): array
    {
        return [
            GraphQL::type('ownable'),
        ];
    }
}
