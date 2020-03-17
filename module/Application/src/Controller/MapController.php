<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Service\MapService;

class MapController extends AbstractActionController
{

    var $adapter;

    public function __construct($adapter)
    {

        $this->adapter = $adapter;
    }

    public function selectRegionAction()
    {
        // Return JSON
        $resultJSON = array(
            'region' => array(),
            'points' => array(),
        );

        // Region GID from URL
        $regionID = $this->params()->fromRoute('id');

        if (is_numeric($regionID)) {
            $ms = new MapService($this->adapter);

            // Get region parameters
            $regionParameters = $ms->getRegionData($regionID);
            $resultJSON['region'] = $regionParameters;

            // Get points geoJSON in the given region
            $pointsGeoJSON = $ms->getPointsGeoJsonInRegion($regionID);
            $resultJSON['region']['geojson']['points'] = $pointsGeoJSON;

            // Get points parameters in the given region
            $pointsParameters = $ms->getPointsData($regionID);
            $resultJSON['points'] = $pointsParameters;
        }

        // Set the Minimum, Maximum, Average and Median of points dataset
        $values = array();
        foreach ($pointsParameters as $point) {
            $values[] = $point['value'];
        }
        if (count($values) > 0) {
            $resultJSON['region']['info'] = array(
                'min' => (float) min($values),
                'max' => (float) max($values),
                'avg' => (float) (array_sum($values) / count($values)),
                'med' => (float) ($this->median($values)),
            );
        }

        return new JsonModel($resultJSON);
    }

    /**
     * Get the median of an array
     * 
     * @param $array
     * 
     * @return number|false 
     */
    private function median($array)
    {
        $iCount = count($array);
        if (is_array($array) && $iCount > 0) {
            $middleIndex = floor($iCount / 2);
            sort($array, SORT_NUMERIC);
            $median = $array[$middleIndex];
            if ($iCount % 2 == 0) {
              $median = ($median + $array[$middleIndex - 1]) / 2;
            }
            return $median;
        }
        return false;
    }

}
