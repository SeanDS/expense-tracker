<?php $this->layout('template', ['page' => 'login', 'title' => 'Login' . (!is_null($title) ? " - $title" : "")]) ?>
        <div class="container">
            <h1>Login</h1>
            <?=$this->section('content')?>
        </div>