<?php

namespace CelcatManagement\CelcatReaderBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use \CelcatManagement\CelcatReaderBundle\Models\Group;

class GroupToXMLFileTransformer implements DataTransformerInterface {

    private $groupManager;

    public function __construct(\CelcatManagement\CelcatReaderBundle\Models\GroupManager $groupManager) {
        $this->groupManager = $groupManager;
    }

    /**
     * @param  Group|null $group
     * @return string
     */
    public function transform($group) {
        return $group;
    }

    /**
     * Transforms a string (number) to an object (group).
     *
     * @param  string $number
     * @return Group|null
     * @throws TransformationFailedException if object (group) is not found.
     */
    public function reverseTransform($number) {
        if (!$number) {
            return null;
        }

        return $number;
    }

}
