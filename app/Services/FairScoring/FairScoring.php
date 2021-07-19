<?php

namespace App\Services\FairScoring;

use App\Models\Resource;
use App\Enums\ResourceType;
use App\Services\FairScoring\Enums\FairSection;
use App\Services\FairScoring\Exceptions\InvalidDataException;
use App\Services\FairScoring\Exceptions\InvalidFairSection;
use App\Services\FairScoring\Rules\Findable\ResourceHasTitle;

class FairScoring
{
    private $type = null;
    private $record = null;

    private $rules = [
        ResourceType::DATASET => [
            FairSection::FINDABLE => [
                ResourceHasTitle::class,
            ],
            FairSection::ACCESSIBLE => [],
            FairSection::INTEROPERABLE => [],
            FairSection::REUSABLE => [],
        ],
    ];

    private function setType($type)
    {
        $this->type = $type;
    }

    private function setRecord($record)
    {
        $this->record = $record;
    }

    private function getRuleReferences($section = null)
    {
        if ($this->type === null) {
            throw new InvalidDataException();
        }

        if ($section === null) {
            return collect($this->rules[$this->type])->flatten(1);
        }

        if (!FairSection::hasValue($section)) {
            throw new InvalidFairSection();
        }

        return collect($this->rules[$this->type][$section]);
    }

    private function getCompleteRuleBody($rule, $score)
    {
        $meetsCondition = $rule::meetsCondition($this->record);
        return [
            'metadataCondition' => $rule::$metadataCondition,
            'scoring' => $rule::$scoring,
            'recommendation' => $rule::$recommendation,
            'score' => $score,
            'meetsCondition' => $meetsCondition,
        ];
    }

    public function for(Resource $resource)
    {
        $this->setType($resource->type);
        $this->setRecord($resource->getMetadataRecord());
        return $this;
    }

    public function getRuleDescriptions($section = null)
    {
        $ruleRefs = $this->getRuleReferences($section);
        return $ruleRefs->map(function ($r) {
            return collect([
                'metadataCondition' => $r::$metadataCondition,
                'scoring' => $r::$scoring,
                'recommendation' => $r::$recommendation,
            ]);
        });
    }

    public function calculateScore($section = null)
    {
        if ($this->type === null || $this->record === null) {
            throw new InvalidDataException();
        }

        if ($section !== null && FairSection::hasValue($section)) {
            return $this->getRuleReferences($section)
                ->reduce(function ($total, $rule) {
                    return $total += $rule::calculateScore($this->record);
                }, 0);
        }

        $findable_score = $this->getRuleReferences(FairSection::FINDABLE)
            ->reduce(function ($total, $rule) {
                return $total += $rule::calculateScore($this->record);
            }, 0);

        $accessible_score = $this->getRuleReferences(FairSection::ACCESSIBLE)
            ->reduce(function ($total, $rule) {
                return $total += $rule::calculateScore($this->record);
            }, 0);

        $interoperable_score = $this->getRuleReferences(FairSection::INTEROPERABLE)
            ->reduce(function ($total, $rule) {
                return $total += $rule::calculateScore($this->record);
            }, 0);

        $reusable_score = $this->getRuleReferences(FairSection::REUSABLE)
            ->reduce(function ($total, $rule) {
                return $total += $rule::calculateScore($this->record);
            }, 0);


        return [
            'findable_score' => $findable_score,
            'accessible_score' => $accessible_score,
            'interoperable_score' => $interoperable_score,
            'reusable_score' => $reusable_score,
        ];
    }

    public function getResult()
    {
        if ($this->type === null || $this->record === null) {
            throw new InvalidDataException();
        }

        $findable = collect([
            'score' => 0,
            'rules' => collect([])
        ]);

        $accessible = collect([
            'score' => 0,
            'rules' => collect([])
        ]);

        $interoperable = collect([
            'score' => 0,
            'rules' => collect([])
        ]);

        $reusable = collect([
            'score' => 0,
            'rules' => collect([])
        ]);

        $this->getRuleReferences(FairSection::FINDABLE)->map(function ($rule) use ($findable) {
            $score = $rule::calculateScore($this->record);
            $findable['score'] += $score;
            $findable['rules']->add($this->getCompleteRuleBody($rule, $score));
        });

        $this->getRuleReferences(FairSection::ACCESSIBLE)->map(function ($rule) use ($accessible) {
            $score = $rule::calculateScore($this->record);
            $accessible['score'] += $score;
            $accessible['rules']->add($this->getCompleteRuleBody($rule, $score));
        });

        $this->getRuleReferences(FairSection::INTEROPERABLE)->map(function ($rule) use ($interoperable) {
            $score = $rule::calculateScore($this->record);
            $interoperable['score'] += $score;
            $interoperable['rules']->add($this->getCompleteRuleBody($rule, $score));
        });

        $this->getRuleReferences(FairSection::REUSABLE)->map(function ($rule) use ($reusable) {
            $score = $rule::calculateScore($this->record);
            $reusable['score'] += $score;
            $reusable['rules']->add($this->getCompleteRuleBody($rule, $score));
        });

        return collect([
            'findable' => $findable,
            'accessible' => $accessible,
            'interoperable' => $interoperable,
            'reusable' => $reusable,
        ]);
    }
}
