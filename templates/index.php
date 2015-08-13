<?php $this->layout('template', ['page' => 'index', 'title' => 'Expenses']) ?>
        <div class="container">
            <p class="lead">Hello, <strong><?=$this->e($user->getAttribute('username'))?></strong>!</p>
            <p>Here are your recent expenses:</p>
            <?php $this->insert('expenses-list') ?>
        </div>