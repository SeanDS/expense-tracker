<?php $this->layout('locations-template', ['title' => 'Delete']) ?>
<h2>Delete Location</h2>
<?php $this->insert('locations-delete-form', ['location' => $location, 'locations' => $locations]) ?>