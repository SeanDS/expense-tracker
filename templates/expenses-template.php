<?php $this->layout('template', ['page' => 'expenses', 'title' => 'Expenses' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Expenses</h1>
            <?=$this->section('content')?>
        </div>