<?php $this->layout('template', ['title' => 'Error']) ?>
        <div class="container">
            <h1>Error</h1>
            <?=$this->e($message)?>
        </div>