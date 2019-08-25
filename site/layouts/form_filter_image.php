<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
$d 				= $displayData;
$displayData 	= null;
$dParamAttr		= str_replace(array('[',']'), '', $d['param']);
$iconType		= $d['s']['i']['icon-type'];

if ($d['params']['open_filter_panel'] == 0) {
    $d['collapse_class'] = $d['s']['c']['panel-collapse.collapse'];
    $d['triangle_class'] = $d['s']['i']['triangle-right'];
} else if ($d['params']['open_filter_panel'] == 2) {
    $d['collapse_class'] = $d['s']['c']['panel-collapse.collapse'];// closed as default and wait if there is some active item to open it
    $d['triangle_class'] = $d['s']['i']['triangle-right'];
} else {
    $d['collapse_class'] = $d['s']['c']['panel-collapse.collapse.in'];
    $d['triangle_class'] = $d['s']['i']['triangle-bottom'];
}

// This output is outside html to get the useful information from foreach about if the panel is active (this is needed to open or clode the panel)
$output = '';
foreach ($d['items'] as $k => $v) {

    $checked 	= '';
    $checkedInt = 0;
    $value		= htmlspecialchars($v->alias);
    if (isset($d['nrinalias']) && $d['nrinalias'] == 1) {
        $value 		= (int)$v->id .'-'. htmlspecialchars($v->alias);
    }

    if (in_array($value, $d['getparams'])) {
        $checked 	= 'checked';
        $checkedInt	= 0;
    } else {
        $checkedInt	= 1;
    }

    $class = $iconType . ' ';
    if ($checked) {
        $class .= 'on';
        $d['collapse_class'] = $d['s']['c']['panel-collapse.collapse.in'];
    }

    if (isset($v->image_small) && $v->image_small != '') {

        $linkI 		= JURI::base(true).'/'.$d['pathitem']['orig_rel'].'/'.$v->image_small;

        $output .= '<a href="#" class="phSelectBoxImage '.$class.'" onclick="phChangeFilter(\''.$d['param'].'\', \''. $value.'\', '.(int)$checkedInt.', \''.$d['formtype'].'\', \''.$d['uniquevalue'].'\');return false;" title="'.htmlspecialchars($v->title).'">'
        .'<img style="'.$d['style'].'" src="'.$linkI.'" alt="'.$v->title.'" />'
        .'</a>';
    }
}
?>
<div class="<?php echo $d['s']['c']['panel.panel-default'] ?>">
	<div class="<?php echo $d['s']['c']['panel-heading'] ?>" role="tab" id="heading<?php echo $dParamAttr; ?>">
		<h4 class="<?php echo $d['s']['c']['panel-title'] ?>">
			<a data-toggle="collapse" href="#collapse<?php echo $dParamAttr; ?>" aria-expanded="true" aria-controls="collapse<?php echo $dParamAttr; ?>" class="panel-collapse"><span class="<?php echo $d['triangle_class'] ?>"></span></a>
			<a data-toggle="collapse" href="#collapse<?php echo $dParamAttr; ?>" aria-expanded="true" aria-controls="collapse<?php echo$dParamAttr; ?>" class="panel-collapse"><?php echo $d['title'] ?></a>
		</h4>
	</div>

	<div id="collapse<?php echo $dParamAttr; ?>" class="<?php echo $d['collapse_class'] ?>" role="tabpanel" aria-labelledby="heading<?php echo $dParamAttr; ?>">
		<div class="<?php echo $d['s']['c']['panel-body'] ?> ph-panel-body-color">
			<div class="ph-mod-color-box">
			<?php echo $output ?>
            </div>
		</div>
	</div>
</div>
