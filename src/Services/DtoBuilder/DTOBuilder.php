<?php

namespace App\Services\DtoBuilder;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOBuilder
{
    private Serializer $serializer;
    private Request $request;
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $encoder = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);
        $normalizer = [new ArrayDenormalizer(), new ObjectNormalizer($classMetadataFactory, null, null, $extractor)];
        $this->serializer = new Serializer($normalizer, $encoder);
        $this->validator = $validator;
    }

    public function buildDTO(string $class)
    {
        $dto = $this->serializer->deserialize($this->request->getContent(), $class, 'json');
        $errors = $this->validator->validate($dto);

        if(count($errors) > 0) {
            throw new InvalidDTOException($errors);
        }

        return $dto;
    }

    public function normalize(object $object)
    {
        $array = $this->serializer->normalize($object);

        return $array;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }
}