<?php
	/*
		*
		* Vulkan hardware capability database server implementation
		*
		* Copyright (C) 2016 by Sascha Willems (www.saschawillems.de)
		*
		* This code is free software, you can redistribute it and/or
		* modify it under the terms of the GNU Affero General Public
		* License version 3 as published by the Free Software Foundation.
		*
		* Please review the following information to ensure the GNU Lesser
		* General Public License version 3 requirements will be met:
		* http://www.gnu.org/licenses/agpl-3.0.de.html
		*
		* The code is distributed WITHOUT ANY WARRANTY; without even the
		* implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
		* PURPOSE.  See the GNU AGPL 3.0 for more details.
		*
	*/

	// Navigation
	echo "<div>";
	echo "<ul class='nav nav-tabs'>";
	echo "	<li class='active'><a data-toggle='tab' href='#memory-tabs-1'>Memory types</a></li>";
	echo "	<li><a data-toggle='tab' href='#memory-tabs-2'>Memory heaps</a></li>";
	echo "</ul>";
	echo "</div>";

	echo "<div class='tab-content'>";

	// Memory types

	echo "<div id='memory-tabs-1' class='tab-pane fade in active reportdiv'>";

	echo "<table id='memory-types' width='100%' class='table table-striped table-bordered'>";
	echo "<thead><tr><td class='caption'>Property</td>";
	foreach ($reportids as $reportId)
	{
		echo "<td class='caption'>Report $reportId</td>";
	}
	echo "</tr></thead><tbody>";

	// Get memory types for each selected report into an array
	$memoryFlags = array();
	$memoryHeapIndices = array();
	$memoryCounts = array();
	$maxMemoryCount = 0;

	$reportIndex = 0;
	foreach ($reportids as $repid)
	{
		$str = "select propertyflags,heapindex from devicememorytypes where reportid = $repid";

		$sqlresult = mysql_query($str);
		$subarray = array();
		$memoryCounts[$reportIndex] = 0;
		while($row = mysql_fetch_row($sqlresult))
		{
			$memoryFlags[$reportIndex][] = $row[0];
			$memoryHeapIndices[$reportIndex][] = $row[1];
			$memoryCounts[$reportIndex]++;
		}
		//$extarray[] = $subarray;
		$reportIndex++;
	}

	$reportIndex = 0;
	foreach ($reportids as $repid)
	{
		if ($memoryCounts[$reportIndex] > $maxMemoryCount)
		{
			$maxMemoryCount = $memoryCounts[$reportIndex];
		}
		$reportIndex++;
	}

	// Generate table
	$colspan = count($reportids) + 1;

	reportCompareDeviceColumns($deviceinfo_captions, $deviceinfo_data, sizeof($reportids));

	// Memory type counts
	echo "<tr class='firstrow'><td class='firstrow'>Memory type count</td>";
	for ($i = 0, $arrsize = sizeof($extarray); $i < $arrsize; ++$i)
	{
		echo "<td>".$memoryCounts[$i]."</td>";
	}
	echo "</tr>";

	for ($i = 0; $i < $maxMemoryCount; ++$i)
	{
		echo "<tr><td class='caption' colspan=".$colspan.">Memory type ".$i."</td></tr>";
		// Heap index
		echo "<tr><td class='key'>Heapindex</td>";
		$index = 0;
		foreach ($reportids as $repid)
		{
			if ($i < $memoryCounts[$index])
			{
				echo "<td>".$memoryHeapIndices[$index][$i]."</td>";
			}
			else
			{
				echo "<td><font color=#BABABA>n/a</font></td>";
			}
			$index++;
		}
		echo "</tr>";
 		// Flags
		echo "<tr><td class='key'>Flags</td>";
		$index = 0;
		foreach ($reportids as $repid)
		{
			echo "<td>";

			if ($i < $memoryCounts[$index])
			{
				$flags = getMemoryTypeFlags($memoryFlags[$index][$i]);
				if (sizeof($flags) > 0)
				{
					foreach ($flags as $flag)
					{
						echo $flag."<br>";
					}
				}
				else
				{
					echo "none";
				}
			}
			else
			{
				echo "<font color=#BABABA>n/a</font>";
			}

			echo "</td>";
			$index++;
		}
		echo "</tr>";
 		// Flags
	}
	echo "</tbody></table></div>";

	// Memory heaps
	echo "<div id='memory-tabs-2' class='tab-pane fade reportdiv'>";

?>
	<div class="alert alert-warning" role="alert">
		<b>Note:</b> Listing may contain memory heaps with host sizes!
	</div>
	<?

	echo "<table id='memory-heaps' width='100%' class='table table-striped table-bordered'>";
	echo "<thead><tr><td class='caption'>Property</td>";
	foreach ($reportids as $reportId)
	{
		echo "<td class='caption'>Report $reportId</td>";
	}
	echo "</tr></thead><tbody>";

	// Get memory types for each selected report into an array
	$memoryHeapSizes = array();
	$memoryHeapFlags = array();
	$memoryHeapCounts = array();
	$maxMemoryHeapCount = 0;

	$reportIndex = 0;
	foreach ($reportids as $repid)
	{
		$str = "select size,flags from devicememoryheaps where reportid = $repid";

		$sqlresult = mysql_query($str);
		$subarray = array();
		$memoryHeapCounts[$reportIndex] = 0;
		while($row = mysql_fetch_row($sqlresult))
		{
			$memoryHeapSizes[$reportIndex][] = $row[0];
			$memoryHeapFlags[$reportIndex][] = $row[1];
			$memoryHeapCounts[$reportIndex]++;
		}
		//$extarray[] = $subarray;
		$reportIndex++;
	}

	$reportIndex = 0;
	foreach ($reportids as $repid)
	{
		if ($memoryHeapCounts[$reportIndex] > $maxMemoryHeapCount)
		{
			$maxMemoryHeapCount = $memoryHeapCounts[$reportIndex];
		}
		$reportIndex++;
	}

	// Generate table
	$colspan = count($reportids) + 1;

	reportCompareDeviceColumns($deviceinfo_captions, $deviceinfo_data, sizeof($reportids));

	// Memory type counts
	echo "<tr class='firstrow'><td class='firstrow'>Memory heap count</td>";
	for ($i = 0, $arrsize = sizeof($extarray); $i < $arrsize; ++$i)
	{
		echo "<td>".$memoryHeapCounts[$i]."</td>";
	}
	echo "</tr>";

	for ($i = 0; $i < $maxMemoryHeapCount; ++$i)
	{
		echo "<tr><td class='caption' colspan=".$colspan.">Memory heap ".$i."</td></tr>";
		// Heap index
		echo "<tr><td class='key'>Size</td>";
		$index = 0;
		foreach ($reportids as $repid)
		{
			if ($i < $memoryHeapCounts[$index])
			{
				echo "<td>".number_format($memoryHeapSizes[$index][$i])."</td>";
			}
			else
			{
				echo "<td><font color=#BABABA>n/a</font></td>";
			}
			$index++;
		}
		echo "</tr>";
 		// Flags
		echo "<tr><td class='key'>Flags</td>";
		$index = 0;
		foreach ($reportids as $repid)
		{
			echo "<td>";

			if ($i < $memoryHeapCounts[$index])
			{
				$flags = getMemoryHeapFlags($memoryHeapFlags[$index][$i]);
				if (sizeof($flags) > 0)
				{
					foreach ($flags as $flag)
					{
						echo $flag."<br>";
					}
				}
				else
				{
					echo "none";
				}
			}
			else
			{
				echo "<font color=#BABABA>n/a</font>";
			}

			echo "</td>";
			$index++;
		}
		echo "</tr>";
 		// Flags
	}

	echo "</tbody></table></div>";

	echo "</div>";
?>