<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\Template;
use App\Entity\User;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;
use RuntimeException;

class TemplateManager
{

    public function getTemplateComputed(Template $tpl, array $data) : Template
    {
        if (!$tpl) {
            throw new RuntimeException('no tpl given');
        }
        $tpl->subject = $this->computeText($tpl->subject, $data);
        $tpl->content = $this->computeText($tpl->content, $data);

        return $tpl;
    }

    private function computeText(string $text, array $data) : ?string
    {
        $applicationContext = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $quoteRepository = QuoteRepository::getInstance()->getById($quote->id);
            $siteRepository = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationRepository = DestinationRepository::getInstance()->getById($quote->destinationId);

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');
            $containsDestinationName  = strpos($text, '[quote:destination_name]');
            $containsDestinationLink  = strpos($text, '[quote:destination_link]');

            if( $containsDestinationLink)
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);

            if ($containsSummaryHtml !== false) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($quoteRepository),
                    $text
                );
            }

            if ($containsSummary) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($quoteRepository),
                    $text
                );
            }
            if ( $containsDestinationName ) 
                $text = str_replace('[quote:destination_name]',$destinationRepository->countryName,$text);

            if ( $containsDestinationLink)
                $text = str_replace('[quote:destination_link]', $siteRepository->url . '/' . $destination->countryName . '/quote/' . $quoteRepository->id, $text);
            else
                $text = str_replace('[quote:destination_link]', '', $text);
        }

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $applicationContext->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
