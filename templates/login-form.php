<div class="row">
    <?php if ($badCredentials): ?>
    <p class="text-danger">Invalid credentials. Please check your spelling.</p>
    <?php endif; ?>
    <p class="text-primary">Use the form below to login.</p>
    <form class="form-horizontal" role="form" action="login.php" method="post">
        <div class="form-group">
            <label class="control-label col-md-1" for="username">Username</label>
            <div class="col-md-3">
                <input class="form-control" type="text" name="username" id="username"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-1" for="password">Password</label>
            <div class="col-md-3">
                <input class="form-control" type="password" name="password" id="password"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-1 col-md-3">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </div>
    </form>
</div>