<form action="types.php?do=moveexpenses&amp;id=<?=$this->e($type->getId())?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="newtypeid" class="control-label col-md-2">Move expenses to</label>
        <div class="col-md-10">
            <?php $this->insert('types-select-list', ['types' => $types, 'selectedTypeId' => 0, 'ignoreTypeIds' => [$type->getId()], 'selectClass' => 'form-control', 'formName' => 'newtypeid', 'formId' => 'newtypeid']) ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Move</button>
        </div>
    </div>
</form>