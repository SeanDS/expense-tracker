<?php $this->layout('template', ['page' => 'types', 'title' => 'Types - Move Expenses']) ?>
        <div class="container">
            <h1>Types</h1>
            <h2>Move Expenses</h2>
            <?php if ($expenses->count()): ?>
            <?php $this->insert('types-move-expenses-form', ['type' => $type, 'types' => $types]) ?>
            <?php else: ?>
            <p class="text-warning">There are no expenses associated with this type.</p>
            <?php endif; ?>
        </div>