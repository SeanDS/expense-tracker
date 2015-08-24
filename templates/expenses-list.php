<?php if ($message == 'editsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Expense saved.
</div>
<?php elseif ($message == 'newsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Expense inserted.
</div>
<?php elseif ($message == 'deletesuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Expense deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-2">Date</th>
        <th class="col-md-2">Type</th>
        <th class="col-md-1">Amount</th>
        <th class="col-md-3">Location</th>
        <th class="col-md-2">Comment</th>
        <th class="col-md-2">Actions</th>
    </thead>
    <tbody>
    <?php if ($expenses->count()): ?>
        <?php foreach($expenses->get() as $expense): ?>
        <tr>
            <td><?=$expense->getDate($user, false)?></td>
            <td><?=$expense->getType()->getAttribute('name')?></td>
            <td>£<?=$expense->getAttribute('amount')?></td>
            <td><?=$expense->getLocation()->getDescription()?></td>
            <td><?=$expense->getAttribute('comment')?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="index.php?do=edit&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="index.php?do=delete&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No expenses.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>