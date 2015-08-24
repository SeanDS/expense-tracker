<h2>Info</h2>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-4">Attribute</th>
        <th class="col-md-8">Details</th>
    </thead>
    <tbody>
        <tr>
            <td>Date</td>
            <td><?=$this->e($expense->getDate())?></td>
        </tr>
        <tr>
            <td>Type</td>
            <td><a href="types.php?do=view&amp;id=<?=$this->e($expense->getType()->getId())?>"><?=$expense->getType()->getAttribute('name')?></a></td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>£<?=$expense->getAttribute('amount')?></td>
        </tr>
        <tr>
            <td>Location</td>
            <td><a href="locations.php?do=view&amp;id=<?=$this->e($expense->getLocation()->getId())?>"><?=$expense->getLocation()->getDescription()?></a></td>
        </tr>
        <tr>
            <td>Comment</td>
            <td><?=$expense->getAttribute('comment')?></td>
        </tr>
    </tbody>
</table>
<div class="btn-group">
    <a href="index.php?do=edit&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-default">Edit</a>
    <a href="index.php?do=delete&amp;id=<?=$this->e($expense->getId())?>" class="btn btn-xs btn-danger">Delete</a>
</div>