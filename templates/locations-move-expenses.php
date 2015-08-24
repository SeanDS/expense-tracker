<?php $this->layout('template', ['page' => 'locations', 'title' => 'Locations - Move Expenses']) ?>
        <div class="container">
            <h1>Locations</h1>
            <h2>Move Expenses</h2>
            <?php if ($expenses->count()): ?>
            <?php $this->insert('locations-move-expenses-form', ['location' => $location, 'locations' => $locations]) ?>
            <?php else: ?>
            <p class="text-warning">There are no expenses associated with this location.</p>
            <?php endif; ?>
        </div>