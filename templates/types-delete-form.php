<form action="types.php?do=delete&amp;id=<?=$this->e($type->getId())?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="newtypeid" class="control-label col-md-2">Move expenses to</label>
        <div class="col-md-10">
            <select class="form-control" name="newtypeid" id="newtypeid">
                <?php foreach($types->get() as $newType): ?>
                <?php if ($newType->getId() != $type->getId()): ?>
                <option value="<?=$this->e($newType->getId())?>"><?=$this->e($newType->getAttribute('name'))?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-danger">Delete</button>
        </div>
    </div>
</form>