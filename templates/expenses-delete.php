<?php $this->layout('expenses-template', ['title' => 'Delete']) ?>
<h2>Delete Expense</h2>
<?php $this->insert('expenses-delete-form', ['expense' => $expense]) ?>