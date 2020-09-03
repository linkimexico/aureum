<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_j2store_categories
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
 * Helper for mod_j2store_categories
 *
 * @package     Joomla.Site
 * @subpackage  mod_j2store_categories
 *
 * @since       1.5
 */
abstract class ModJ2storeCategoriesHelper
{
    /**
     * Get list of articles
     *
     * @param   JRegistry &$params module parameters
     *
     * @return  array
     *
     * @since   1.5
     */
    public static function getList(&$params)
    {
        $category_display_view = $params->get('category_display_view', 'list_view');
        $maxlevel = $params->get('maxlevel', '0');
        $parent_category = $params->get('parent', 'root');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('level,id')->from('#__categories')->where('id=' . $parent_category);
        $db->setQuery($query);
        $result = $db->loadObject();
        $parent_level = $result->level;
        if ($maxlevel) {
            $maxlevel = $parent_level + $maxlevel;
        }
        $options = array();
        $options['countItems'] = $params->get('numitems', 0);
        $options['maxlevel'] = $maxlevel;
        $options['parent_level'] = $parent_level;
        $id = (int)$parent_category;
        if ($parent_category !== 'root') {
            if ($id == 0) {
                $id = 'root';
            }
        }
        $user = JFactory::getUser();
        $query = $db->getQuery(true);
        $query->select('c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
			c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
			c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
			c.path, c.published, c.rgt, c.title, c.modified_user_id, c.version');
        $case_when = ' CASE WHEN ';
        $case_when .= $query->charLength('c.alias', '!=', '0');
        $case_when .= ' THEN ';
        $c_id = $query->castAsChar('c.id');
        $case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
        $case_when .= ' ELSE ';
        $case_when .= $c_id . ' END as slug';
        $query->select($case_when)->from('#__categories as c')->where('(c.extension="com_content" OR c.extension=' . $db->quote('system') . ')');
        $query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
        $query->where('c.published = 1');
        $query->where('c.extension = "com_content"');
        $query->where('c.id != ' . (int)$id);
        $query->order('c.lft');
        if ($id != 'root') {
            $query->join('LEFT', '#__categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)')
                ->where('s.id=' . (int)$id);
        }
        $subQuery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ' .
            'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension ="com_content"  AND parent.published != 1 GROUP BY cat.id) ';
        $query->join('LEFT', $subQuery . 'AS badcats ON badcats.id = c.id')->where('badcats.id is null');
        if (isset($options['countItems']) && $options['countItems'] == 1) {
            $query->join(
                'LEFT', $db->quoteName("#__content") . ' AS i ON i.' . $db->quoteName("catid") . ' = c.id AND i.state = 1'
            );
            // here let us join our j2store products with content
            $query->select('#__j2store_products.product_source_id');
            $query->join('LEFT', '#__j2store_products ON #__j2store_products.product_source=' . $db->q('com_content') . ' AND #__j2store_products.product_source_id = i.id');
            $query->select('COUNT(i.id) AS numitems');
        }
        if ($maxlevel > 0) {
            $query->where('c.level <=' . $maxlevel);
            $query->where('c.level >=' . $parent_level);
        }
        if ($params->get('show_children', 0)) {
            $query->where('c.parent_id =' . $id);
        }
        // Group by
        $query->group(
            'c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
			 c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
			 c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
			 c.path, c.published, c.rgt, c.title, c.modified_user_id, c.version'
        );
        $db->setQuery($query);
        $items = $db->loadObjectList('id');
        if ($category_display_view != "grid_view") {
            // Arrange array elements for list view
            $items = ModJ2storeCategoriesHelper::get_child($items, $id);
        }
        if ($params->get('count', 0) > 0 && count($items) > $params->get('count', 0)) {
            $items = array_slice($items, 0, $params->get('count', 0));
        }
        return $items;
    }

    /**
     * Arranging array for list view
     * @param $elements
     * @param int $parentId
     * @return array
     */
    public static function get_child($elements, $parentId = 0)
    {
        $branch = array();
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = ModJ2storeCategoriesHelper::get_child($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
