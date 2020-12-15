<?php


namespace Smile\Common\GraphQL\Entity;

use Smile\Common\Support\Parent\BaseEntity;

/**
 * Class TPaginatorQo
 * @package App\Support\GraphQL\Entity
 * @property int $currentPage
 * @property int $pageSize
 */
class TPaginatorQo extends BaseEntity
{
    public function __construct($data = [])
    {
        parent::__construct($data);
        if (array_key_exists('page', $data)) {
            $this->currentPage = $data['page'];
        }
        if (array_key_exists('pageSize', $data)) {
            $this->pageSize = $data['pageSize'];
        }
    }
}