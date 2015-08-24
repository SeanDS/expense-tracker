<form action="locations.php?do=new" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="organisation" class="control-label col-md-2">Organisation</label>
        <div class="col-md-10">
            <input type="text" class="form-control" name="organisation" id="organisation" placeholder="Organisation" value=""/>
        </div>
    </div>
    <div class="form-group">
        <label for="address" class="control-label col-md-2">Address</label>
        <div class="col-md-10">
            <textarea class="form-control" name="address" id="address" rows="3" placeholder="Address"></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-primary">Insert</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>