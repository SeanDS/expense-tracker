<?php $this->layout('types-template', ['title' => 'Edit']) ?>
<h2>Edit Type</h2>
<?php $this->insert('types-edit-form', ['type' => $type, 'types' => $types]) ?>