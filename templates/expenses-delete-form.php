<form action="index.php?do=delete&amp;id=<?=$this->e($expense->getId())?>" method="post" class="form-horizontal">
    <input type="hidden" name="confirm" value="true"/>
    <p class="text-danger">Are you sure you wish to delete the expense <strong><?=$this->e($expense->getDescription())?></strong>?</p>
    <div class="form-group">
        <div class="col-md-12">
            <button type="submit" class="btn btn-danger">Delete</button>
        </div>
    </div>
</form>