<?php $this->layout('locations-template', ['title' => 'Edit']) ?>
<h2>Edit Location</h2>
<?php $this->insert('locations-edit-form', ['location' => $location]) ?>