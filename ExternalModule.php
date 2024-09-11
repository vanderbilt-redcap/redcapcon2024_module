<?php

namespace Vanderbilt\RCC2024Demo;

use ExternalModules\AbstractExternalModule;
use REDCap;

class ExternalModule extends AbstractExternalModule {

    function redcap_data_entry_page_top($project_id) {
        $project = new \Project();
		echo "<pre>";
		print_r($this->retrieveProjectData($project));
		echo "</pre>";
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

	function compareProjectRecord(\Project $srcProject, \Project $dstProject, $record) : string {
		$recordStatus = "error";
        $srcData = $this->retrieveProjectData($srcProject,[$record]);
        $dstData = $this->retrieveProjectData($dstProject,[$record]);

		if (!empty($dstData)) {
			if ($dstData == $srcData) {
				$recordStatus = "matched";
			} else {
				$recordStatus = "not-matched";
			}
		}
		else {
			$recordStatus = "missing";
		}
		return $recordStatus;
    }

	function retrieveProjectData(\Project $project, array $records = []) : array {
		$projectData = \REDcap::getData([
			'project_id' => $project->project_id,
			'return_format' => 'json-array',
			'records' => $records
		]);

		if (empty($projectData['errors'])) {
			return $projectData;
		}

		return [];
	}
}
