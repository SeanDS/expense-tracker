<?php $this->layout('template', ['page' => 'types', 'title' => 'Types' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Types</h1>
            <?=$this->section('content')?>
        </div>