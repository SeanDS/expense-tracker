<?php $this->layout('template', ['page' => 'locations', 'title' => 'Locations']) ?>
        <div class="container">
            <h1>Locations</h1>
            <h2>List</h2>
            <?php $this->insert('locations-list') ?>
            <p><a href="locations.php?do=new" class="btn btn-success">New</a></p>
        </div>