<?php $this->layout('template', ['page' => 'locations', 'title' => 'Locations - Edit Location']) ?>
        <div class="container">
            <h1>Locations</h1>
            <h2>Edit Location</h2>
            <?php $this->insert('locations-edit-form', ['location' => $location]) ?>
        </div>