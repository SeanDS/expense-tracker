<?php $this->layout('template', ['page' => 'types', 'title' => 'Types']) ?>
        <div class="container">
            <h1>Types</h1>
            <h2>List</h2>
            <?php $this->insert('types-list') ?>
            <p><a href="types.php?do=new" class="btn btn-success">New</a></p>
        </div>