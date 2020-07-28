<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Cart
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
$r               = $this->r;
$user            = JFactory::getUser();
$userId          = $user->get('id');
$listOrder       = $this->escape($this->state->get('list.ordering'));
$listDirn        = $this->escape($this->state->get('list.direction'));
$canOrder        = $user->authorise('core.edit.state', $this->t['o']);
$saveOrder       = $listOrder == 'a.ordering';
$saveOrderingUrl = '';
if ($saveOrder && !empty($this->items)) {
    $saveOrderingUrl = $r->saveOrder($this->t, $listDirn);
}
$sortFields = $this->getSortFields();

echo $r->jsJorderTable($listOrder);


echo $r->startForm($this->t['o'], $this->t['tasks'], 'adminForm');
//echo $r->startFilter();
//echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.published'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
//echo $r->selectFilterCategory(PhocaDownloadCategory::options($this->t['o']), 'JOPTION_SELECT_CATEGORY', $this->state->get('filter.category_id'));
//echo $r->endFilter();

echo $r->startMainContainer();
/*
echo $r->startFilterBar();
echo $r->inputFilterSearch($this->t['l'] . '_FILTER_SEARCH_LABEL', $this->t['l'] . '_FILTER_SEARCH_DESC',
    $this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);

echo $r->startFilterBar(2);
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.published'));
echo $r->endFilterBar();

echo $r->endFilterBar();*/

echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);
echo '<th class="ph-product">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_PRODUCT', 'productname', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-category">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_CATEGORY', 'cattitle', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-name">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_NAME', 'a.name', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-email">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_EMAIL', 'a.email', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-phone">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_PHONE', 'a.phone', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-ip">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_IP', 'a.ip', $listDirn, $listOrder) . '</th>' . "\n";
//echo '<th class="ph-published">'.Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-message">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_MESSAGE', 'a.message', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-date">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_DATE', 'a.date', $listDirn, $listOrder) . '</th>' . "\n";
echo '<th class="ph-id">' . Joomla\CMS\HTML\HTMLHelper::_('searchtools.sort', $this->t['l'] . '_ID', 'a.id', $listDirn, $listOrder) . '</th>' . "\n";

echo $r->endTblHeader();

echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

$originalOrders = array();
$parentsStr     = "";
$j              = 0;

if (is_array($this->items)) {
    foreach ($this->items as $i => $item) {
        //if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
        $j++;

        $urlEdit    = 'index.php?option=' . $this->t['o'] . '&task=' . $this->t['task'] . '.edit&id=';
        $urlTask    = 'index.php?option=' . $this->t['o'] . '&task=' . $this->t['task'];
        $orderkey   = array_search($item->id, $this->ordering[$item->product_id]);
        $ordering   = ($listOrder == 'a.ordering');
        $canCreate  = $user->authorise('core.create', $this->t['o']);
        $canEdit    = $user->authorise('core.edit', $this->t['o']);
        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
        $canChange  = $user->authorise('core.edit.state', $this->t['o']) && $canCheckin;
        $linkEdit   = JRoute::_($urlEdit . $item->id);


        echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);

        echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
        echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);

        $checkO = '';
        if ($item->checked_out) {
            $checkO .= Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'] . '.', $canCheckin);
        }

        // Product
        $item->productname = !isset($item->productname) ? JText::_('COM_PHOCACART_NO_PRODUCT') : $item->productname;
        if ($canCreate || $canEdit) {
            $checkO .= '<a href="' . JRoute::_($linkEdit) . '">' . $this->escape($item->productname) . '</a>';
        } else {
            $checkO .= $this->escape($item->productname);
        }

        echo $r->td($checkO, "small");

        // Category
        $item->cattitle = !isset($item->cattitle) ? JText::_('COM_PHOCACART_NO_PRODUCT') : $item->cattitle;
        echo $r->td($this->escape($item->cattitle), "small");

        // Name
        $nameSuffix = '';
        if (isset($item->reviewname) && isset($item->reviewusername) && $item->reviewname != '' && $item->reviewusername != '') {
            $nameSuffix = ' <small>(' . $item->reviewname . ' - ' . $item->reviewusername . ')</small>';
        }
        echo $r->td($this->escape($item->name . $nameSuffix), "small");


        echo $r->td($this->escape($item->email), "small");
        echo $r->td($this->escape($item->phone), "small");
        echo $r->td($this->escape($item->ip), "small");

        //echo $r->td(Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small");

        echo $r->td(PhocacartUtils::wordDeleteWhole($item->message, 50), "small");

        echo $r->td(JHtml::date($item->date, JText::_('DATE_FORMAT_LC5')), "small");

        echo $r->td($item->id, "small");

        echo $r->endTr();

        //}
    }
}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 11);
echo $r->endTable();

echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>
