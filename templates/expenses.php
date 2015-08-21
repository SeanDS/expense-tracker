<?php $this->layout('template', ['page' => 'expenses', 'title' => 'Expenses']) ?>
        <div class="container">
            <h1>Expenses</h1>
            <h2>List</h2>
            <?php $this->insert('expenses-list', ['expenses' => $expenses]) ?>
            <p><a href="index.php?do=new" class="btn btn-success">New</a></p>
        </div>