<table class="table table-bordered table-hover table-striped">
    <thead>
        <th>Date</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Location</th>
    </thead>
    <tbody>
    <?php if ($expenses->count() > 0): ?>
        <?php foreach($expenses->get() as $expense): ?>
        <tr>
            <td><?=$expense->getDate($user, false)?></td>
            <td><?=$expense->getType()->getAttribute('name')?></td>
            <td>£<?=$expense->getAttribute('amount')?></td>
            <td><?=$expense->getLocation()->getAddress()?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No expenses.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>