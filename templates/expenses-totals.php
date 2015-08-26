<h2>Totals</h2>
<table class="table table-bordered table-hover table-striped">
    <thead>
        <th class="col-md-4">Range</th>
        <th class="col-md-8">Expenditure</th>
    </thead>
    <tbody>
        <?php foreach($totals as $total): ?>
        <tr>
            <td><?=$this->e($total['range'])?></td><td><?=$this->e(sprintf("£%.2f", $total['amount']))?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>