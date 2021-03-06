<?php

namespace Devinweb\TestParallel\Util;

class Parser
{
    public function parseEnv(string $phpunit_xml_path)
    {
        $fileContents= file_get_contents($phpunit_xml_path);
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
        $fileContents = trim(str_replace('"', "'", $fileContents));
        $simpleXml = simplexml_load_string($fileContents);
        $json = json_encode($simpleXml);
        $arrOutput = json_decode($json, true);

        return $this->getEnvAttributes($arrOutput);
    }


    protected function getEnvAttributes(array $config)
    {
        $xml_data =  collect($config);
        $env_data = collect($xml_data->get('php'))->get('env');
        return collect($env_data)->reduce(function ($carry, $item) {
            $attributes = $item['@attributes'];
            $carry[$attributes['name']] = $attributes['value'];
            return $carry;
        }, []);
    }
}
