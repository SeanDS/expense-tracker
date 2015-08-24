<h2>List</h2>
<?php if ($message == 'editsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Location saved.
</div>
<?php elseif ($message == 'newsuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Location inserted.
</div>
<?php elseif ($message == 'moveexpensessuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Expenses moved.
</div>
<?php elseif ($message == 'deletesuccess'): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Location deleted.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-2">Organisation</th>
        <th class="col-md-6">Address</th>
        <th class="col-md-1">Expenses</th>
        <th class="col-md-3">Actions</th>
    </thead>
    <tbody>
        <?php foreach($locations->get() as $location): ?>
        <tr>
            <td><a href="locations.php?do=view&amp;id=<?=$this->e($location->getId())?>"><?=$this->e($location->getAttribute('organisation'))?></a></td>
            <td><address><?=$location->getFormattedAddress()?></address></td>
            <td class="text-center"><?=$this->e($location->getExpenseCount())?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="locations.php?do=edit&amp;id=<?=$this->e($location->getId())?>" class="btn btn-xs btn-default">Edit</a>
                    <a href="locations.php?do=moveexpenses&amp;id=<?=$this->e($location->getId())?>" class="btn btn-xs btn-default">Move Expenses</a>
                    <a href="locations.php?do=delete&amp;id=<?=$this->e($location->getId())?>" class="btn btn-xs btn-danger">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>    
</table>