<?php $this->layout('expenses-template') ?>
<?php $this->insert('expenses-list', ['expenses' => $expenses, 'message' => $message]) ?>
<?php $this->insert('expenses-totals', ['totals' => $totals]) ?>
<p><a href="index.php?do=new" class="btn btn-success">New</a></p>