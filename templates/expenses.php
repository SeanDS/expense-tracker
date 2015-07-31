<table class="table table-bordered table-hover table-striped">
    <thead>
        <th>Date</th>
        <th>Type</th>
        <th>Location</th>
    </thead>
    <tbody>
    <?php if ($expenses->getCount() > 0): ?>
        <?php foreach($expenses as $expense): ?>
        <tr>
            <td>date</td>
            <td>type</td>
            <td>location</td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No expenses.</td>
        </tr>
    <?php endif; ?>
    </tbody>    
</table>