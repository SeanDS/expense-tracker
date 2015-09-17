<form action="types.php?do=edit&amp;id=<?=$this->e($type->getId())?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="name" class="control-label col-md-2">Name</label>
        <div class="col-md-10">
            <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?=$this->e($type->getAttribute('name'))?>"/>
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="control-label col-md-2">Description</label>
        <div class="col-md-10">
            <textarea class="form-control" name="description" id="description" rows="3" placeholder="Description"><?=$this->e($type->getAttribute('description'))?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="parenttypeid" class="control-label col-md-2">Parent</label>
        <div class="col-md-10">
            <?php $this->insert('types-select-list', ['types' => $types, 'defaultSelect' => true, 'defaultSelectValue' => '0', 'defaultSelectName' => '', 'defaultSelectId' => 0, 'selectedTypeId' => $type->getAttribute('parenttypeid'), 'ignoreTypeIds' => [$type->getId()], 'selectClass' => 'form-control', 'formName' => 'parenttypeid', 'formId' => 'parenttypeid']) ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>