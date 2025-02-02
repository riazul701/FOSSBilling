<?php

/**
 * FOSSBilling.
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license   Apache-2.0
 *
 * Copyright FOSSBilling 2022
 * This software may contain code previously used in the BoxBilling project.
 * Copyright BoxBilling, Inc 2011-2021
 *
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */

/**
 * News and announcements management.
 */

namespace Box\Mod\News\Api;

class Guest extends \Api_Abstract
{
    /**
     * Get paginated list of active news items.
     *
     * @return array
     */
    public function get_list($data)
    {
        $data['status'] = 'active';
        [$sql, $params] = $this->getService()->getSearchQuery($data);
        $per_page = $data['per_page'] ?? $this->di['pager']->getPer_page();
        $page = $data['page'] ?? null;
        $pager = $this->di['pager']->getSimpleResultSet($sql, $params, $per_page, $page);
        foreach ($pager['list'] as $key => $item) {
            $post = $this->di['db']->getExistingModelById('Post', $item['id'], 'Post not found');
            $pager['list'][$key] = $this->getService()->toApiArray($post);
        }

        return $pager;
    }

    /**
     * Get news item by ID or SLUG.
     *
     * @param int    $id   - news item ID. Required only if SLUG is not passed.
     * @param string $slug - news item slug. Required only if ID is not passed.
     *
     * @return array
     */
    public function get($data)
    {
        if (!isset($data['id']) && !isset($data['slug'])) {
            throw new \Box_Exception('ID or slug is missing');
        }

        $id = $data['id'] ?? null;
        $slug = $data['slug'] ?? null;

        if ($id) {
            $model = $this->getService()->findOneActiveById($id);
        } else {
            $model = $this->getService()->findOneActiveBySlug($slug);
        }

        if (!$model || 'active' !== $model->status) {
            throw new \Box_Exception('News item not found');
        }

        return $this->getService()->toApiArray($model);
    }
}
