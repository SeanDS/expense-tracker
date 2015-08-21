<?php $this->layout('template', ['page' => 'types', 'title' => 'Types - Edit Type']) ?>
        <div class="container">
            <h1>Types</h1>
            <h2>Edit Type</h2>
            <?php $this->insert('types-edit-form', ['type' => $type]) ?>
        </div>