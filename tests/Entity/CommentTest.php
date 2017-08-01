<?php namespace Tests;

use BookStack\Page;
use BookStack\Comment;

class CommentTest extends BrowserKitTest
{

    public function test_add_comment()
    {
        $this->asAdmin();
        $page = $this->getPage();

        $this->addComment($page);
    }

    public function test_comment_reply()
    {
        $this->asAdmin();
        $page = $this->getPage();

        // add a normal comment
        $createdComment = $this->addComment($page);

        // reply to the added comment
        $this->addComment($page, $createdComment['id']);
    }

    public function test_comment_edit()
    {
        $this->asAdmin();
        $page = $this->getPage();

        $createdComment = $this->addComment($page);
        $comment = [
            'id' => $createdComment['id'],
            'page_id' => $createdComment['page_id']
        ];
        $this->updateComment($comment);
    }

    public function test_comment_delete()
    {
        $this->asAdmin();
        $page = $this->getPage();

        $createdComment = $this->addComment($page);

        $this->deleteComment($createdComment['id']);
    }

    private function getPage() {
        $page = Page::first();
        return $page;
    }


    private function addComment($page, $parentCommentId = null) {
        $comment = factory(Comment::class)->make();
        $url = "/ajax/page/$page->id/comment/";
        $request = [
            'text' => $comment->text,
            'html' => $comment->html
        ];
        if (!empty($parentCommentId)) {
            $request['parent_id'] = $parentCommentId;
        }
        $this->call('POST', $url, $request);

        $createdComment = $this->checkResponse();
        return $createdComment;
    }

    private function updateComment($comment) {
        $tmpComment = factory(Comment::class)->make();
        $url = '/ajax/page/' . $comment['page_id'] . '/comment/ ' . $comment['id'];
         $request = [
            'text' => $tmpComment->text,
            'html' => $tmpComment->html
        ];

        $this->call('PUT', $url, $request);

        $updatedComment = $this->checkResponse();
        return $updatedComment;
    }

    private function deleteComment($commentId) {
        //  Route::delete('/ajax/comment/{id}', 'CommentController@destroy');
        $url = '/ajax/comment/' . $commentId;
        $this->call('DELETE', $url);

        $deletedComment = $this->checkResponse();
        return $deletedComment;
    }

    private function checkResponse() {
        $expectedResp = [
            'status' => 'success'
        ];

        $this->assertResponseOk();
        $this->seeJsonContains($expectedResp);

        $resp = $this->decodeResponseJson();
        $createdComment = $resp['comment'];
        $this->assertArrayHasKey('id', $createdComment);

        return $createdComment;
    }
}
