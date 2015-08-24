<?php $this->layout('expenses-template', ['title' => 'New']) ?>
<h2>New Expense</h2>
<?php $this->insert('expenses-new-form', ['types' => $types, 'locations' => $locations]) ?>