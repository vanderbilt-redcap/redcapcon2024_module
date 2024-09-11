<?php

namespace Vanderbilt\RCC2024Demo;

use ExternalModules\AbstractExternalModule;
use REDCap;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        $url = $_SERVER['REQUEST_URI'];
        var_dump($url);
    }


    protected function includeCss(string $path) {
        echo '<link rel="stylesheet" href="' . $this->getUrl($path) . '">';
    }


    protected function includeJs(string $path) {
        echo '<script src="' . $this->getUrl($path) . '"></script>';
    }


    protected function setJsSettings(array $settings) {
        foreach ($settings as $k => $v) {
            $this->framework->tt_addToJavascriptModuleObject($k, $v);
        }
    }


    function cron1($cron_info) {
        echo "I am a cron!";
        sleep(60*5 + 30);
        return true;
    }

    function compareProjectData(\Project $srcProject, \Project $dstProject) {
        $returnRecords = []
        $srcData = $this->retrieveProjectData($srcProject);
        $dstData = $this->retrieveProjectData($dstProject);

        foreach ($srcData as $srcRecord => $data) {
            if (isset($dstData[$srcRecord])) {
                $returnRecords[$srcRecord] = 
            }
        }
    }

    function retrieveProjectData(\Project $project) {
        $returnData = [];
        $projectData = \REDcap::getData([
            'project_id' => $project->project_id,
            'return_format' => 'json-array'
        ]);

        if (empty($projectData['errors'])) {
            foreach ($projectData as $data) {
                if ($data[$project->table_pk] != "") {
                    $returnData[$data[$project->table_pk]] = $data;
                }
            }
        }

        return $returnData;
    }
}
