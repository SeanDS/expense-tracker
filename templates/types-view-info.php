<h2>Info</h2>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-4">Attribute</th>
        <th class="col-md-8">Details</th>
    </thead>
    <tbody>
        <tr>
            <td>Name</td>
            <td><?=$this->e($type->getAttribute('name'))?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?=$this->e($type->getAttribute('description'))?></td>
        </tr>
        <tr>
            <td>Expense count</td>
            <td><?=$this->e($type->getExpenseCount())?></td>
        </tr>
    </tbody>
</table>
<?php if ($expenses->count()): ?>
<h2>Expenses</h2>
<?php $this->insert('expenses-list', ['expenses' => $expenses]) ?>
<?php endif; ?>
<div class="btn-group">
    <a href="types.php?do=edit&amp;id=<?=$this->e($type->getId())?>" class="btn btn-xs btn-default">Edit</a>
    <a href="types.php?do=moveexpenses&amp;id=<?=$this->e($type->getId())?>" class="btn btn-xs btn-default">Move Expenses</a>
    <a href="types.php?do=delete&amp;id=<?=$this->e($type->getId())?>" class="btn btn-xs btn-danger">Delete</a>
</div>