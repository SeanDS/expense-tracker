<?php $this->layout('types-template', ['title' => 'Move Expenses']) ?>
<h2>Move Expenses</h2>
<?php if ($expenses->count()): ?>
<?php $this->insert('types-move-expenses-form', ['type' => $type, 'types' => $types]) ?>
<?php else: ?>
<p class="text-warning">There are no expenses associated with this type.</p>
<?php endif; ?>