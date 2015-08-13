<?php if ($message): ?>
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Type edits saved.
</div>
<?php endif; ?>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th>Name</th>
        <th>Description</th>
        <th>Actions</th>
    </thead>
    <tbody>
    <?php if ($types->count() > 0): ?>
        <?php foreach($types->get() as $type): ?>
        <tr>
            <td><?=$this->e($type->getAttribute('name'))?></td>
            <td><?=$this->e($type->getAttribute('description'))?></td>
            <td><a href="types.php?do=edit&amp;id=<?=$this->e($type->getAttribute('typeid'))?>" class="btn btn-default">Edit</a></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No types.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>