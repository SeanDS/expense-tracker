<form action="locations.php?do=moveexpenses&amp;id=<?=$this->e($location->getId())?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="newlocationid" class="control-label col-md-2">Move expenses to</label>
        <div class="col-md-10">
            <select class="form-control" name="newlocationid" id="newlocationid">
                <?php foreach($locations->get() as $newLocation): ?>
                <?php if ($newLocation->getId() != $location->getId()): ?>
                <option value="<?=$this->e($newLocation->getId())?>"><?=$this->e($newLocation->getDescription())?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Move</button>
        </div>
    </div>
</form>