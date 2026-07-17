<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Tests\TestCase;

class EntityQueryTest extends TestCase
{
    public function test_basic_entity_query_has_join_and_type_applied()
    {
        $query = Book::query();
        $expected = 'select * from `entities` left join `entity_container_data` on `entity_container_data`.`entity_id` = `entities`.`id` and `entity_container_data`.`entity_type` = ? where `type` = ? and `entities`.`deleted_at` is null';
        $this->assertEquals($expected, $query->toSql());
        $this->assertEquals(['book', 'book'], $query->getBindings());
    }

    public function test_joins_in_sub_queries_use_alias_names()
    {
        $query = Book::query()->whereHas('chapters', function (Builder $query) {
            $query->where('name', '=', 'a');
        });

        // Probably from type limits on relation where not needed?
        $expected = 'select * from `entities` left join `entity_container_data` on `entity_container_data`.`entity_id` = `entities`.`id` and `entity_container_data`.`entity_type` = ? where exists (select * from `entities` as `laravel_reserved_%d` left join `entity_container_data` on `entity_container_data`.`entity_id` = `laravel_reserved_%d`.`id` and `entity_container_data`.`entity_type` = ? where `entities`.`id` = `laravel_reserved_%d`.`book_id` and `name` = ? and `type` = ? and `laravel_reserved_%d`.`deleted_at` is null) and `type` = ? and `entities`.`deleted_at` is null';
        $this->assertStringMatchesFormat($expected, $query->toSql());
        $this->assertEquals(['book', 'chapter', 'a', 'chapter', 'book'], $query->getBindings());
    }

    public function test_book_chapter_relation_applies_type_condition()
    {
        $book = $this->entities->book();
        $query = $book->chapters();
        $expected = 'select * from `entities` left join `entity_container_data` on `entity_container_data`.`entity_id` = `entities`.`id` and `entity_container_data`.`entity_type` = ? where `entities`.`book_id` = ? and `entities`.`book_id` is not null and `type` = ? and `entities`.`deleted_at` is null';
        $this->assertEquals($expected, $query->toSql());
        $this->assertEquals(['chapter', $book->id, 'chapter'], $query->getBindings());

        $query = Book::query()->whereHas('chapters');
        $expected = 'select * from `entities` left join `entity_container_data` on `entity_container_data`.`entity_id` = `entities`.`id` and `entity_container_data`.`entity_type` = ? where exists (select * from `entities` as `laravel_reserved_%d` left join `entity_container_data` on `entity_container_data`.`entity_id` = `laravel_reserved_%d`.`id` and `entity_container_data`.`entity_type` = ? where `entities`.`id` = `laravel_reserved_%d`.`book_id` and `type` = ? and `laravel_reserved_%d`.`deleted_at` is null) and `type` = ? and `entities`.`deleted_at` is null';
        $this->assertStringMatchesFormat($expected, $query->toSql());
        $this->assertEquals(['book', 'chapter', 'chapter', 'book'], $query->getBindings());
    }
}
