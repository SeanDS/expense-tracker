<?php $this->layout('types-template') ?>
<?php $this->insert('types-list', ['types' => $types, 'message' => $message]) ?>
<p><a href="types.php?do=new" class="btn btn-success">New</a></p>