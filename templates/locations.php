<?php $this->layout('locations-template') ?>
<?php $this->insert('locations-list', ['locations' => $locations, 'message' => $get['message']]) ?>
<p><a href="locations.php?do=new" class="btn btn-success">New</a></p>