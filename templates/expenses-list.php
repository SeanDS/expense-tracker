<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-2">Date</th>
        <th class="col-md-1">Type</th>
        <th class="col-md-1">Amount</th>
        <th class="col-md-4">Location</th>
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
            <td><?=$expense->getLocation()->getAddress()?></td>
            <td><?=$expense->getAttribute('comment')?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="index.php?do=edit&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="index.php?do=changetype&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-default">Change Type</a>
                    <a href="index.php?do=delete&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No expenses.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>