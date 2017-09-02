<div id="page-comments" page-id="<?= $page->id ?>" class="comments-list" v-cloak>
  <h3>@{{totalCommentsStr}}</h3>
  <hr>
  <comment v-for="(comment, index) in comments" :initial-comment="comment" :index="index" :level=1
     v-on:comment-added.stop="commentAdded"
     :current-user-id="currentUserId" :key="comment.id" :permissions="permissions"></comment>
  <div v-if="canComment">
     <comment-reply v-on:comment-added.stop="commentAdded" :page-id="<?= $page->id ?>">
     </comment-reply>
  </div>
</div>