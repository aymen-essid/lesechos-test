<?php

namespace App\Repository;

use App\Entity\Quote;
use App\Helper\SingletonTrait;
use DateTime;
use Faker\Factory;

class QuoteRepository implements RepositoryInterface
{
    use SingletonTrait;

    /**
     * @param int $id
     *
     * @return Quote
     */
    public function getById($id)
    {
        // DO NOT MODIFY THIS METHOD
        $generator = Factory::create();
        $generator->seed($id);

        return new Quote(
            $id,
            $generator->numberBetween(1, 10),
            $generator->numberBetween(1, 200),
            new DateTime()
        );
    }
}
