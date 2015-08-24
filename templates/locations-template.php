<?php $this->layout('template', ['page' => 'locations', 'title' => 'Locations' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Locations</h1>
            <?=$this->section('content')?>
        </div>