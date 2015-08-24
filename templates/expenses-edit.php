<?php $this->layout('template', ['page' => 'expenses', 'title' => 'Expenses - Edit Expense']) ?>
        <div class="container">
            <h1>Expenses</h1>
            <h2>Edit Expense</h2>
            <?php $this->insert('expenses-edit-form', ['expense' => $expense, 'types' => $types, 'locations' => $locations]) ?>
        </div>