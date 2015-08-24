<?php $this->layout('template', ['page' => 'locations', 'title' => 'Locations - Delete Location']) ?>
        <div class="container">
            <h1>Locations</h1>
            <h2>Delete Location</h2>
            <?php $this->insert('locations-delete-form', ['location' => $location, 'locations' => $locations]) ?>
        </div>