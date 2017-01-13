<?php

namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

use BookStack\Http\Requests;

class CommentController extends Controller
{
    
    public function add(Request $request, $pageId) {
        // $this->checkOwnablePermission('page-view', $page);
    }
    
    public function update(Request $request, $id) {
        // Check whether its an admin or the comment owner.
        // $this->checkOwnablePermission('page-view', $page);
    }
    
    public function destroy($id) {
        // Check whether its an admin or the comment owner.
        // $this->checkOwnablePermission('page-view', $page);
    }
    
    public function getLastXComments($pageId) {
        // $this->checkOwnablePermission('page-view', $page);
    }
    
    public function getChildComments($pageId, $id) {
        // $this->checkOwnablePermission('page-view', $page);
    }
}
