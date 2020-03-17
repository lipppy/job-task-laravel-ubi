<?php 
namespace Application\Service;

class MapService
{
    private $adapter;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get all region GID & NAME parameter for the menu
     * 
     * @return Array
     */
    public function getRegionsForMenu()
    {
        $regions = array();

        $sql = 'SELECT gid, name FROM regions ORDER BY gid ASC';
        $query = $this->adapter->query($sql);
        $result = $query->execute();
        if ($result->count() > 0) {
            while ($result->valid()) {
                $regions[] = $result->current();
                $result->next();
            }
        }

        return $regions;
    }

    /**
     * Select region data by GID
     * Return array contains region geoJSON structure
     * 
     * @param Number Region GID
     * 
     * @return Array
     */
    public function getRegionData($regionID)
    {
        $resultJSON = array();

        $sql = "SELECT
                    gid,
                    name,
                    json_build_object(
                        'type', 'Feature',
                        'id', gid,
                        'geometry', ST_AsGeoJSON(the_geom)::json,
                        'properties', json_build_object(
                            'id', gid,
                            'name', name
                        )
                    ) AS geojson
                FROM
                    regions
                WHERE
                    gid = {$regionID}";
        $query = $this->adapter->query($sql);
        $result = $query->execute();
        if ($result->count() == 1) {
            while ($result->valid()) {
                $row = $result->current();
                $resultJSON = array(
                    'id' => $row['gid'],
                    'name' => $row['name'],
                    'geojson' => array(
                        'region' => json_decode($row['geojson']),
                    ),
                );
                $result->next();
            }
        }

        return $resultJSON;
    }

    /**
     * Get points geoJSON in region by GID
     * Return array contains points geoJSON structure
     * 
     * @param Number Region GID
     * 
     * @return Array
     */
    public function getPointsGeoJsonInRegion($regionID)
    {
        $resultJSON = array();

        $sql = "SELECT
                    jsonb_build_object(
                        'type',     'FeatureCollection',
                        'crs', json_build_object(
                            'type', 'name',
                            'properties', json_build_object(
                                'name', 'EPSG:3857'
                            )
                        ),
                        'features', jsonb_agg(feature)
                    ) AS geojson
                FROM (
                    SELECT 
                        jsonb_build_object(
                            'type',       'Feature',
                            'id',         inputs.gid,
                            'geometry',   ST_AsGeoJSON(inputs.the_geom)::jsonb,
                            'properties', to_jsonb(inputs) - 'the_geom'
                        ) AS feature
                    FROM ( 
                        SELECT
                            points.gid,
                            points.value,
                            points.the_geom
                        FROM
                            points
                        JOIN
                            regions
                        ON
                            ST_Contains(regions.the_geom, points.the_geom)
                        WHERE
                            regions.gid = {$regionID}
                    ) AS inputs
                ) AS features;";
        $query = $this->adapter->query($sql);
        $result = $query->execute();
        if ($result->count() == 1) {
            while ($result->valid()) {
                $row = $result->current();
                $resultJSON = json_decode($row['geojson']);
                $result->next();
            }
        }

        return $resultJSON;
    }

    /**
     * Get points geoJSON in region by GID
     * Return array contains points geoJSON structure
     * 
     * @param Number Region GID
     * 
     * @return Array
     */
    public function getPointsData($regionID)
    {
        $resultArray = array();

        $sql = "SELECT
                    points.gid,
                    points.value
                FROM
                    points
                JOIN
                    regions ON ST_Contains(regions.the_geom, points.the_geom)
                WHERE
                    regions.gid = {$regionID};";
        $query = $this->adapter->query($sql);
        $result = $query->execute();
        if ($result->count() > 0) {
            while ($result->valid()) {
                $row = $result->current();
                $resultArray[] = array(
                    'id' => $row['gid'],
                    'value' => $row['value'],
                );
                $result->next();
            }
        }

        return $resultArray;
    }


}