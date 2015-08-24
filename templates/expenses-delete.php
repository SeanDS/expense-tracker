<?php $this->layout('template', ['page' => 'expenses', 'title' => 'Expenses - Delete Expense']) ?>
        <div class="container">
            <h1>Expenses</h1>
            <h2>Delete Expense</h2>
            <?php $this->insert('expenses-delete-form', ['expense' => $expense]) ?>
        </div>