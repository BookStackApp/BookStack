<?php namespace BookStack\Actions;

class ActivityType
{
    const PAGE_CREATE = 'page_create';
    const PAGE_UPDATE = 'page_update';
    const PAGE_DELETE = 'page_delete';
    const PAGE_RESTORE = 'page_restore';
    const PAGE_MOVE = 'page_move';
    const COMMENTED_ON = 'commented_on';
    const CHAPTER_CREATE = 'chapter_create';
    const CHAPTER_UPDATE = 'chapter_update';
    const CHAPTER_DELETE = 'chapter_delete';
    const CHAPTER_MOVE = 'chapter_move';
    const BOOK_CREATE = 'book_create';
    const BOOK_UPDATE = 'book_update';
    const BOOK_DELETE = 'book_delete';
    const BOOK_SORT = 'book_sort';
    const BOOKSHELF_CREATE = 'bookshelf_create';
    const BOOKSHELF_UPDATE = 'bookshelf_update';
    const BOOKSHELF_DELETE = 'bookshelf_delete';
}