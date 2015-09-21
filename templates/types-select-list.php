<select class="<?=$this->e($selectClass)?>" name="<?=$this->e($formName)?>" id="<?=$this->e($formId)?>">
    <?php if (isset($defaultSelect) && $defaultSelect): ?>
    <option value="<?=$this->e($defaultSelectValue)?>"<?php if ($selectedTypeId == $defaultSelectId): ?> selected<?php endif; ?>><?=$this->e($defaultSelectName)?></option>
    <?php endif; ?>
    <?=$this->tree()->toList($types->toTree(), $selectedTypeId, $ignoreTypeIds)?>
</select>