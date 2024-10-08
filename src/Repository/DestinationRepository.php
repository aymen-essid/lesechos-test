<?php

namespace App\Repository;

use App\Entity\Destination;
use App\Helper\SingletonTrait;
use Faker\Factory;

class DestinationRepository implements RepositoryInterface
{
    use SingletonTrait;
    /**
     * @param int $id
     *
     * @return Destination
     */
    public function getById($id)
    {
        // DO NOT MODIFY THIS METHOD
        $generator    = Factory::create();
        $generator->seed($id);

        return new Destination(
            $id,
            $generator->country,
            'en',
            $generator->slug()
        );
    }
}
