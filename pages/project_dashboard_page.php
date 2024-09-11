<?php

namespace Vanderbilt\RCC2024Demo;

$module = new RCC2024Demo();
$module->includeCss('css/module_style.css');
$records = $module->retrieveRecordList($project_id);

$this_project = new \Project($project_id);

$source_pid = $module->framework->getProjectSetting("src-project");
$source_project = new \Project($source_pid);

echo "<table><tr><th>Record</th><th>Status</th></tr>";
foreach ($records as $this_record) {
	$result = $module->compareProjectRecord($this_project, $source_project, $this_record);
	echo $module->printRecordStatusRow($this_record,$result);
}
echo "</table>";
