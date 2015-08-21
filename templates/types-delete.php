<?php $this->layout('template', ['page' => 'types', 'title' => 'Types - Delete Type']) ?>
        <div class="container">
            <h1>Types</h1>
            <h2>Delete Type</h2>
            <?php $this->insert('types-delete-form', ['type' => $type, 'types' => $types]) ?>
        </div>