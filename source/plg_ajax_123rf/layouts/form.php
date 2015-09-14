<?php
$file = dirname(__FILE__) . '/form.xml';
$form = JForm::getInstance('ja123rf', $file);
$fieldsets = $form->getFieldsets();

foreach ($fieldsets as $name => $fieldset) {
  ?>
  <div class="fieldset">
    <?php echo $form->renderFieldSet($name); ?>
  </div>
  <?php
}
