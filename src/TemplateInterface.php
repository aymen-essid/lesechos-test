<?php

namespace App;

use App\Entity\Template;

interface TemplateInterface
{
    public function getTemplateComputed(Template $tpl, array $data) : Template;
}