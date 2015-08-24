<?php $this->layout('expenses-template', ['title' => 'View']) ?>
<h2>View Expense</h2>
<?php $this->insert('expenses-view-info', ['expense' => $expense]) ?>