<?php

use Illuminate\Database\Seeder;

class DummyContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(BookStack\User::class, 1)->create();
        $role = \BookStack\Role::where('name', '=', 'admin')->first();
        $user->attachRole($role);


        $books = factory(BookStack\Book::class, 20)->create(['created_by' => $user->id, 'updated_by' => $user->id])
            ->each(function($book) use ($user) {
                $chapters = factory(BookStack\Chapter::class, 5)->create(['created_by' => $user->id, 'updated_by' => $user->id])
                    ->each(function($chapter) use ($user, $book){
                       $pages = factory(\BookStack\Page::class, 10)->make(['created_by' => $user->id, 'updated_by' => $user->id, 'book_id' => $book->id]);
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = factory(\BookStack\Page::class, 3)->make(['created_by' => $user->id, 'updated_by' => $user->id]);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });
    }
}
