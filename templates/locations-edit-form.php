<form action="locations.php?do=edit&amp;id=<?=$this->e($location->getId())?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="organisation" class="control-label col-md-2">Name</label>
        <div class="col-md-10">
            <input type="text" class="form-control" name="organisation" id="organisation" placeholder="Organisation" value="<?=$this->e($location->getAttribute('organisation'))?>"/>
        </div>
    </div>
    <div class="form-group">
        <label for="address" class="control-label col-md-2">Address</label>
        <div class="col-md-10">
            <textarea class="form-control" name="address" id="address" rows="3" placeholder="Address"><?=$this->e($location->getAttribute('address'))?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>