<?php

namespace AppBundle\Api;

use LastFmApi\Api\BaseApi;

/**
 * Allows access to the api requests relating to chart
 */
class ChartApi extends BaseApi
{

    /**
     * Get the top tracks chart
     * @param array $methodVars An array with the optional value: <i>limit</i>, <i>page</i>
     * @return bool
     * @throws \LastFmApi\Exception\CacheException
     */
    public function getTopTracks($methodVars)
    {
        $vars = array(
            'method' => 'chart.gettoptracks',
            'api_key' => $this->auth->apiKey
        );
        $vars = array_merge($vars, $methodVars);

        if ($call = $this->apiGetCall($vars)) {
            $i = 0;
            foreach ($call->tracks->track as $track) {
                $topTracks[$i]['name'] = (string) $track->name;
                $topTracks[$i]['playcount'] = (string) $track->playcount;
                $topTracks[$i]['listeners'] = (string) $track->listeners;
                $topTracks[$i]['mbid'] = (string) $track->mbid;
                $topTracks[$i]['url'] = (string) $track->url;
                $topTracks[$i]['streamable'] = (string) $track->streamable;
                $topTracks[$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
                $topTracks[$i]['artist']['name'] = (string) $track->artist->name;
                $topTracks[$i]['artist']['mbid'] = (string) $track->artist->mbid;
                $topTracks[$i]['artist']['url'] = (string) $track->artist->url;
                $topTracks[$i]['image']['small'] = (string) $track->image[0];
                $topTracks[$i]['image']['medium'] = (string) $track->image[1];
                $topTracks[$i]['image']['large'] = (string) $track->image[2];
                $i++;
            }

            return $topTracks;
        } else {
            return false;
        }
    }

}
