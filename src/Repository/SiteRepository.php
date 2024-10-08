<?php

namespace App\Repository;

use App\Entity\Site;
use App\Helper\SingletonTrait;
use Faker\Factory;

class SiteRepository implements RepositoryInterface
{
    use SingletonTrait;

    private $url;

    /**
     * @param int $id
     *
     * @return Site
     */
    public function getById($id)
    {
        // DO NOT MODIFY THIS METHOD
        $generator = Factory::create();
        $generator->seed($id);

        return new Site($id, $generator->url);
    }
}
