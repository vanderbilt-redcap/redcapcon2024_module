<?php

namespace Vanderbilt\RCC2024Demo;

use ExternalModules\AbstractExternalModule;
use REDCap;

class RCC2024Demo extends AbstractExternalModule {

    function redcap_data_entry_form_top($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) {
        $this_project = new \Project($project_id);

        $source_pid = $this->framework->getProjectSetting("src-project");
        $source_project = new \Project($source_pid);
        $result = $this->compareProjectRecord($this_project, $source_project, $record);

        var_dump($result);

        echo file_get_contents("record_div.html");
    }

	public function printOutSomething($project_id) {
		echo "You passed me the project ID $project_id";
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
		foreach($this->framework->getProjectsWithModuleEnabled() as $projectId) {
			$project = new \Project($projectId);
			$srcProjectId = $this->framework->getProjectSetting("src-project", $projectId);
			if(!is_numeric($srcProjectId)) {
				error_log("Project Not Configured: ".$this->escape($projectId));
			}
			
			$srcProject = new \Project($srcProjectId);
			$recordData = $this->retrieveProjectData($project);
			foreach($recordData as $thisRecord) {
				$matchStatus = $this->compareProjectRecord($srcProject,$project,$thisRecord);
				if($matchStatus == "not-matched" || $matchStatus == "missing") {
					$saveStatus = \REDCap::saveData([
						"project_id" => $projectId,
						"data" => [$thisRecord],
						"dataFormat" => "json-array",
						"overwriteBehavior" => "overwrite"
					]);
					
					if(isset($saveStatus["errors"])) {
						error_log($saveStatus);
					}
				}
			}
		}
        echo "I am a cron!";
        sleep(60*5 + 30);
        return true;
    }

	function compareProjectRecord(\Project $srcProject, \Project $dstProject, $record) : string {
        if ($srcProject->metadata !== $dstProject->metadata) { return "project mismatch"; }

		$recordStatus = "error";
        $srcData = $this->retrieveProjectData($srcProject,[$record]);
        $dstData = $this->retrieveProjectData($dstProject,[$record]);

		if (!empty($dstData)) {
			if ($dstData === $srcData) {
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
