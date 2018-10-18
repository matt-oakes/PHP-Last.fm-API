<?php

namespace LastFmApi\Api;

/**
 * Allows access to the api requests relating to chart
 */
class ChartApi extends BaseApi
{

    /**
     * Get the top artists chart
     * @param array $methodVars An array with the optional value: <i>limit</i>, <i>page</i>
     * @return array|false
     */
    public function getTopArtists($methodVars)
    {
        $vars = array(
            'method' => 'chart.gettopartists',
            'api_key' => $this->auth->apiKey
        );
        $vars = array_merge($vars, $methodVars);

        if ($call = $this->apiGetCall($vars)) {
            $i = 0;
            foreach ($call->artists->artist as $artist) {
                $topArtists[$i]['name'] = (string) $artist->name;
                $topArtists[$i]['playcount'] = (string) $artist->playcount;
                $topArtists[$i]['listeners'] = (string) $artist->listeners;
                $topArtists[$i]['mbid'] = (string) $artist->mbid;
                $topArtists[$i]['url'] = (string) $artist->url;
                $topArtists[$i]['streamable'] = (string) $artist->streamable;
                $topArtists[$i]['image']['small'] = (string) $artist->image[0];
                $topArtists[$i]['image']['medium'] = (string) $artist->image[1];
                $topArtists[$i]['image']['large'] = (string) $artist->image[2];
                $topArtists[$i]['image']['extralarge'] = (string) $artist->image[3];
                $topArtists[$i]['image']['mega'] = (string) $artist->image[4];
                $i++;
            }

            return $topArtists;
        } else {
            return false;
        }
    }

    /**
     * Get the top tags chart
     * @param array $methodVars An array with the optional value: <i>limit</i>, <i>page</i>
     * @return array|false
     */
    public function getTopTags($methodVars)
    {
        $vars = array(
            'method' => 'chart.gettoptags',
            'api_key' => $this->auth->apiKey
        );
        $vars = array_merge($vars, $methodVars);

        if ($call = $this->apiGetCall($vars)) {
            $i = 0;
            foreach ($call->tags->tag as $tags) {
                $topTags[$i]['name'] = (string) $tags->name;
                $topTags[$i]['playcount'] = (string) $tags->playcount;
                $topTags[$i]['listeners'] = (string) $tags->listeners;
                $topTags[$i]['mbid'] = (string) $tags->mbid;
                $topTags[$i]['url'] = (string) $tags->url;
                $topTags[$i]['streamable'] = (string) $tags->streamable;
                $topTags[$i]['image']['small'] = (string) $tags->image[0];
                $topTags[$i]['image']['medium'] = (string) $tags->image[1];
                $topTags[$i]['image']['large'] = (string) $tags->image[2];
                $topTags[$i]['image']['extralarge'] = (string) $tags->image[3];
                $topTags[$i]['image']['mega'] = (string) $tags->image[4];
                $i++;
            }

            return $topTags;
        } else {
            return false;
        }
    }

    /**
     * Get the top tracks chart
     * @param array $methodVars An array with the optional value: <i>limit</i>, <i>page</i>
     * @return array|false
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
//                $topTracks[$i]['image']['extralarge'] = (string) $track->image[3];
//                $topTracks[$i]['image']['mega'] = (string) $track->image[4];
                $i++;
            }

            return $topTracks;
        } else {
            return false;
        }
    }

}
