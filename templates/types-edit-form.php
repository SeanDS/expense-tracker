<form action="types.php?do=edit&amp;id=<?=$this->e($type->getAttribute('typeid'))?>" method="post">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?=$this->e($type->getAttribute('name'))?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3" placeholder="Description"><?=$this->e($type->getAttribute('description'))?></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>