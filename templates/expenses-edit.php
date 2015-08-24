<?php $this->layout('expenses-template', ['title' => 'Edit']) ?>
<h2>Edit Expense</h2>
<?php $this->insert('expenses-edit-form', ['expense' => $expense, 'types' => $types, 'locations' => $locations]) ?>