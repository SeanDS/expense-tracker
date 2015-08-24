<?php $this->layout('types-template', ['title' => 'Delete']) ?>
<h2>Delete Type</h2>
<?php $this->insert('types-delete-form', ['type' => $type, 'types' => $types]) ?>