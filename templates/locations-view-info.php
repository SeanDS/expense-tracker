<h2>Info</h2>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-4">Attribute</th>
        <th class="col-md-8">Details</th>
    </thead>
    <tbody>
        <tr>
            <td>Organisation</td>
            <td><?=$this->e($location->getAttribute('organisation'))?></td>
        </tr>
        <tr>
            <td>Address</td>
            <td><address><?=$location->getFormattedAddress()?></address></td>
        </tr>
        <tr>
            <td>Expense count</td>
            <td><?=$this->e($location->getExpenseCount())?></td>
        </tr>
    </tbody>
</table>
<?php if ($recentExpenses->count()): ?>
<h2>Recent Expenses</h2>
<?php $this->insert('expenses-list', ['expenses' => $recentExpenses]) ?>
<?php endif; ?>
<div class="btn-group">
    <a href="locations.php?do=edit&amp;id=<?=$this->e($location->getId())?>" class="btn btn-xs btn-default">Edit</a>
    <a href="locations.php?do=moveexpenses&amp;id=<?=$this->e($location->getId())?>" class="btn btn-xs btn-default">Move Expenses</a>
    <a href="locations.php?do=delete&amp;id=<?=$this->e($location->getId())?>" class="btn btn-xs btn-danger">Delete</a>
</div>