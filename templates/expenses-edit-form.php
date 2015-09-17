<form action="index.php?do=edit&amp;id=<?=$this->e($expense->getId())?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="date" class="control-label col-md-2">Date</label>
        <div class="col-md-10">
            <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                <input type="text" class="form-control" name="date" id="date" placeholder="YYYY-MM-DD HH:MM:SS" value="<?=$this->e($expense->getDate())?>"/>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="typeid" class="control-label col-md-2">Type</label>
        <div class="col-md-10">
            <?php $this->insert('types-select-list', ['types' => $types, 'selectedTypeId' => $expense->getAttribute('typeid'), 'selectClass' => 'form-control', 'formName' => 'typeid', 'formId' => 'typeid']) ?>
        </div>
    </div>
    <div class="form-group">
        <label for="amount" class="control-label col-md-2">Amount</label>
        <div class="col-md-10">
            <div class="input-group">
                <div class="input-group-addon">£</div>
                <input type="text" class="form-control" name="amount" id="date" placeholder="00.00" value="<?=$this->e($expense->getAttribute('amount'))?>"/>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="locationid" class="control-label col-md-2">Location</label>
        <div class="col-md-10">
            <select class="form-control" name="locationid" id="locationid">
                <?php foreach($locations->get() as $location): ?>
                <option value="<?=$this->e($location->getId())?>"<?php if($location->getId() == $expense->getAttribute('locationid')): ?> selected<?php endif; ?>><?=$this->e($location->getDescription())?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="comment" class="control-label col-md-2">Comment</label>
        <div class="col-md-10">
            <textarea class="form-control" name="comment" id="comment" rows="3" placeholder="Comment"><?=$this->e($expense->getAttribute('comment'))?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>