<?php $this->layout('locations-template', ['title' => 'View']) ?>
<h2>View Location</h2>
<?php $this->insert('locations-view-info', ['location' => $location, 'recentExpenses' => $recentExpenses]) ?>