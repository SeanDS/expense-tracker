<?php $this->layout('locations-template', ['title' => 'Move Expenses']) ?>
<h2>Move Expenses</h2>
<?php if ($expenses->count()): ?>
<?php $this->insert('locations-move-expenses-form', ['location' => $location, 'locations' => $locations]) ?>
<?php else: ?>
<p class="text-warning">There are no expenses associated with this location.</p>
<?php endif; ?>