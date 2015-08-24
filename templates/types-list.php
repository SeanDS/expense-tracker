<h2>List</h2>
<?php if ($message == 'editsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Type saved.
</div>
<?php elseif ($message == 'newsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Type inserted.
</div>
<?php elseif ($message == 'moveexpensessuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Expenses moved.
</div>
<?php elseif ($message == 'deletesuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Type deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-2">Name</th>
        <th class="col-md-6">Description</th>
        <th class="col-md-1">Expenses</th>
        <th class="col-md-3">Actions</th>
    </thead>
    <tbody>
        <?php foreach($types->get() as $type): ?>
        <tr>
            <td><?=$this->e($type->getAttribute('name'))?></td>
            <td><?=$this->e($type->getAttribute('description'))?></td>
            <td class="text-center"><?=$this->e($type->getExpenseCount())?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="types.php?do=edit&amp;id=<?=$this->e($type->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="types.php?do=moveexpenses&amp;id=<?=$this->e($type->getId())?>" class="btn btn-xs btn-default">Move Expenses</a>
                    <a href="types.php?do=delete&amp;id=<?=$this->e($type->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>    
</table>