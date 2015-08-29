<h2>List</h2>
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
            <td><a href="index.php?do=view&amp;id=<?=$this->e($expense->getId())?>"><?=$expense->getDate()?></a></td>
            <td><a href="types.php?do=view&amp;id=<?=$this->e($expense->getType()->getId())?>"><?=$expense->getType()->getAttribute('name')?></a></td>
            <td>£<?=$this->e($expense->getAttribute('amount'))?></td>
            <td><a href="locations.php?do=view&amp;id=<?=$this->e($expense->getLocation()->getId())?>"><?=$expense->getLocation()->getDescription()?></a></td>
            <td><?=$this->e($expense->getFormattedComment())?></td>
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