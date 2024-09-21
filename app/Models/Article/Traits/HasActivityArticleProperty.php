<?php

namespace App\Models\Article\Traits;

use App\Parser\Article\ArticleParser;
use App\Models\Activity\Traits\HasActivity;
use App\Services\Constant\Activity\ActivityType;

trait HasActivityArticleProperty
{
    use HasActivity;


    /**
     * @return string
     */
    public function getActivityType(): string
    {
        return ActivityType::ARTICLE;
    }

    /**
     * @return string
     */
    public function getActivitySubType(): string
    {
        return '';
    }


    /**
     * @return array
     */
    public function getActivityPropertyCreate()
    {
        return $this->setActivityPropertyParser();
    }

    /**
     * @return array
     */
    public function getActivityPropertyUpdate()
    {
        return $this->setActivityPropertyParser();
    }

    /**
     * @return array
     */
    public function getActivityPropertyDelete()
    {
        return $this->setActivityPropertyParser() + [
                'deletedAt' => $this->deletedAt?->format('d/m/Y H:i')
            ];
    }


    /** --- FUNCTIONS --- */

    /**
     * @return array|null
     */
    private function setActivityPropertyParser()
    {
        $this->refresh();

        return ArticleParser::first($this);
    }

}
