<?php

namespace App\Service\User;

use App\Entity\User;
use App\Entity\Vote;
use DateTime;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomSerializer
{
    public function customSerializer()
    {
        //Special registration date serializer
        $encoder = new JsonEncoder();

        // all callback parameters are optional (you can omit the ones you don't use)
        $dateCallback = function ($innerObject) {
            return $innerObject instanceof DateTime ? $innerObject->format(DateTime::ISO8601) : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'inscriptionDate' => $dateCallback,
                'birthDate' => $dateCallback,
                '$votationDate' => $dateCallback
            ]
        ];

        $normalizer = new GetSetMethodNormalizer(
            null,
            null,
            null,
            null,
            null,
            $defaultContext);

        return new Serializer([$normalizer], [$encoder]);
    }


}
