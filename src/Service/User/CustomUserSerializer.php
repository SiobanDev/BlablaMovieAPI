<?php

namespace App\Service\User;

use DateTime;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomUserSerializer
{
    public function customUserSerializer()
    {
        //Special registration date serializer
        $encoder = new JsonEncoder();

        // all callback parameters are optional (you can omit the ones you don't use)
        $dateCallback = function (
            $innerObject,
            $outerObject,
            string $attributeName = 'inscription_date',
            string $format = null, array $context = []) {
            return $innerObject instanceof DateTime ? $innerObject->format(DateTime::ISO8601) : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'inscription_date' => $dateCallback
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