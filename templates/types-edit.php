<?php $this->layout('template', ['page' => 'types', 'title' => 'Types - Edit']) ?>
        <div class="container">
            <h1>Edit Type</h1>
            <?php $this->insert('types-edit-form', ['type' => $type]) ?>
        </div>