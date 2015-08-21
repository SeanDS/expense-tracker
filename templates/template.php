<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/expenses.css">
        
        <title><?=$this->e($title)?></title>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Expenses</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                      <li<?php if ($page == 'expenses'): ?> class="active"<?php endif; ?>><a href="index.php">Expenses</a></li>
                      <li<?php if ($page == 'types'): ?> class="active"<?php endif; ?>><a href="types.php">Types</a></li>
                      <li<?php if ($page == 'locations'): ?> class="active"<?php endif; ?>><a href="locations.php">Locations</a></li>
                      <li<?php if ($page == 'user'): ?> class="active"<?php endif; ?>><a href="user.php">User</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <p class="lead">Hello, <strong><?=$this->e($user->getAttribute('username'))?></strong>!</p>
        </div>
<?=$this->section('content')?>
        <script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </body>
</html>
