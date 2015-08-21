<?php $this->layout('template', ['page' => 'expenses', 'title' => 'Expenses - New Expense']) ?>
        <div class="container">
            <h1>Expenses</h1>
            <h2>New Expense</h2>
            <?php $this->insert('expenses-new-form', ['types' => $types, 'locations' => $locations]) ?>
        </div>