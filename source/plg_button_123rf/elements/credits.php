<?php
/**
 * $JA#COPYRIGHT$
 */
// No direct to access this file
defined('_JEXEC') or die();

jimport('joomla.filesystem.file');

/**
 * Form Field class for the Joomla Platform.
 * @since       11.1
 */
class JFormFieldCredits extends JFormField
 {
    /**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Credits';
    
    /**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
     protected function getInput()
     {
       
        // Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        
        // Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
        
        $input = '<input style="box-shadow:none; text-align:left;border:none; background: none; color: red; font-weight: bold; font-size: 14px" readonly="true" type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
        $button = '<a class="btn" id="test_' . $this->id . '" href="#" onclick="checkCredit();" title="'.JText::_('123RF_CHECK_CUSTOMER').'" >'.JText::_('123RF_CHECK_CUSTOMER').'</a>';
        
        if(version_compare(JVERSION, '3.0', '>=')) {
			$html = sprintf('%s %s', $button, $input);
		} else {
			$html = sprintf('<div class="fltlft">%s</div><div class="button2-left"><div class="blank">%s</div></div>', $button, $input);
		}
		$script = '<script type="text/javascript">
                        function checkCredit(){
                            var apikey = jQuery("input[name=\'jform[params][apikey]\']").val();
                            var custid = jQuery("input[name=\'jform[params][custid]\']").val();
                            var secretkey = jQuery("input[name=\'jform[params][secretkey]\']").val();
                            var accesskey = jQuery("input[name=\'jform[params][accesskey]\']").val();
                            var params = {secretkey, accesskey, apikey, custid};
                            jQuery.ajax({
                               type: "POST",
                               data: params,
                               url: \''.JUri::base(true).'/index.php?option=com_ajax&plugin=123rf&view=customer&format=html\',
                               dataType: "html",
                               error: function(e){
                                    alert(e.message);
                               },
                               success: function(data){
                                    jQuery("input[name=\''.$this->name.'\']").val(data);
                                } 
                            });
                            
                        }
                   </script>';
        $html .= $script;           
		return $html;
     }
 }