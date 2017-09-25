<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Controller;

use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use ContaoCommunityAlliance\Merger2\Functions\FunctionCollectionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AutoCompleteOptionsController.
 *
 * @package ContaoCommunityAlliance\Merger2\Controller
 */
class AutoCompleteOptionsController
{
    /**
     * Merger² function collection.
     *
     * @var FunctionCollectionInterface
     */
    private $functionCollection;

    /**
     * AutoCompleteOptionsController constructor.
     *
     * @param FunctionCollectionInterface $functionCollection Merger² function collection.
     */
    public function __construct(FunctionCollectionInterface $functionCollection)
    {
        $this->functionCollection = $functionCollection;
    }

    /**
     * Execute the controller.
     *
     * @return JsonResponse
     */
    public function execute()
    {
        $descriptions = array_map(
            function (Description $description) {
                return $description->toArray();
            },
            $this->functionCollection->getDescriptions()
        );

        return new JsonResponse($descriptions);
    }
}
