<?php
function versionToString($version)
{
    $versionStr = ($version >> 22).".".(($version >> 12) & 0x3ff).".".($version & 0xfff);
    return $versionStr;
}

function getFlags($flagList, $flag)
{
	$flags = array();
	
	$arrVals = array_values($flagList);
			
	$index = 0;
	foreach ($flagList as $i => $value)
	{
		if ($flag & $i)
		{
			$flags[] = $arrVals[$index];
		}
		$index++;
	}			
	return $flags;		
}

function getFormatFlags($flag)
{
	$flags = array(
		0x0001 => "SAMPLED_IMAGE_BIT",
		0x0002 => "STORAGE_IMAGE_BIT",
		0x0004 => "STORAGE_IMAGE_ATOMIC_BIT",
		0x0008 => "UNIFORM_TEXEL_BUFFER_BIT",
		0x0010 => "STORAGE_TEXEL_BUFFER_BIT",
		0x0020 => "STORAGE_TEXEL_BUFFER_ATOMIC_BIT",
		0x0040 => "VERTEX_BUFFER_BIT",
		0x0080 => "COLOR_ATTACHMENT_BIT",
		0x0100 => "COLOR_ATTACHMENT_BLEND_BIT",
		0x0200 => "DEPTH_STENCIL_ATTACHMENT_BIT",
		0x0400 => "BLIT_SRC_BIT",
		0x0800 => "BLIT_DST_BIT",
		0x1000 => "SAMPLED_IMAGE_FILTER_LINEAR_BIT",
	);
	return getFlags($flags, $flag);
}

function getImageUsageFlags($flag)
{
	$flags = array(
		0x0001 => "TRANSFER_SRC_BIT",
		0x0002 => "TRANSFER_DST_BIT",
		0x0004 => "SAMPLED_BIT",
		0x0008 => "STORAGE_BIT",
		0x0010 => "COLOR_ATTACHMENT_BIT",
		0x0020 => "DEPTH_STENCIL_ATTACHMENT_BIT",
		0x0040 => "TRANSIENT_ATTACHMENT_BIT",
		0x0080 => "INPUT_ATTACHMENT_BIT",
	);
	return getFlags($flags, $flag);
}

function getSurfaceTransformFlags($flag)
{
	$flags = array(
		0x0001 => "IDENTITY_BIT_KHR",
		0x0002 => "ROTATE_90_BIT_KHR",
		0x0004 => "ROTATE_180_BIT_KHR",
		0x0008 => "ROTATE_270_BIT_KHR",
		0x0010 => "HORIZONTAL_MIRROR_BIT_KHR",
		0x0020 => "HORIZONTAL_MIRROR_ROTATE_90_BIT_KHR",
		0x0040 => "HORIZONTAL_MIRROR_ROTATE_180_BIT_KHR",
		0x0080 => "HORIZONTAL_MIRROR_ROTATE_270_BIT_KHR",
		0x0100 => "INHERIT_BIT_KHR",
	);
	return getFlags($flags, $flag);	
}

function getCompositeAlphaFlags($flag)
{
	$flags = array(
		0x0001 => "OPAQUE_BIT_KHR",
		0x0002 => "PRE_MULTIPLIED_BIT_KHR",
		0x0004 => "POST_MULTIPLIED_BIT_KHR",
		0x0008 => "INHERIT_BIT_KHR",
	);
	return getFlags($flags, $flag);	
}

function getMemoryTypeFlags($flag)
{
	$flags = array(
		0x0001 => "DEVICE_LOCAL_BIT" ,
		0x0002 => "HOST_VISIBLE_BIT",
		0x0004 => "HOST_COHERENT_BIT",
		0x0008 => "HOST_CACHED_BIT",
		0x0010 => "LAZILY_ALLOCATED_BIT",	
	);		
	return getFlags($flags, $flag);
}

function getMemoryHeapFlags($flag)
{
	$flags = array(
		0x0001 => "DEVICE_LOCAL_BIT" ,
	);		
	return getFlags($flags, $flag);
}

function getQueueFlags($flag)
{
	$flags = array(
		0x0001 => "GRAPHICS_BIT" ,
		0x0002 => "COMPUTE_BIT" ,
		0x0004 => "TRANSFER_BIT" ,
		0x0008 => "SPARSE_BINDING_BIT" ,
	);		
	return getFlags($flags, $flag);
}

function getSampleCountFlags($flag)
{
	$flags = array();
	for ($i = 0; $i < 7; ++$i) 
	{
		$flags[pow(2, $i)] = pow(2, $i);
	}
	return getFlags($flags, $flag);
}

// Generate a simple ul/li list for the flags
function listFlags($flags)
{
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

function getPresentMode($value)
{
	$modes = array(
		"IMMEDIATE_KHR" => 0,
		"MAILBOX_KHR" => 1,
		"FIFO_KHR" => 2,
		"FIFO_RELAXED_KHR" => 3,
	);
	if (in_array($value, $modes))
	{
		$key = array_search($value, $modes);
		return $key;
	}
	else
	{
		return "unknown"; 
	}
}

function getColorSpace($value)
{
	$modes = array(
		"SRGB_NONLINEAR_KHR" => 0,
	);
	if (in_array($value, $modes))
	{
		$key = array_search($value, $modes);
		return $key;
	}
	else
	{
		return "unknown"; 
	}
}

// Generate device info table part for report compare pages
function reportCompareDeviceColumns($deviceinfo_captions, $deviceinfo_data, $count)
{
	for ($i = 0; $i < sizeof($deviceinfo_data[0]); ++$i) 
	{
		echo "<tr>";
		echo "<td>".$deviceinfo_captions[$i]."</td>";
		for ($j = 0, $arrsize = $count; $j < $arrsize; ++$j) 				
		{
			echo "<td class='deviceinfo'>".$deviceinfo_data[$j][$i]."</td>";
		}
		echo "</tr>";
	}
}

// Convert vendor specific driver version string
function getDriverVerson($versionraw, $versiontext, $vendorid)
{
	if ($versionraw != '')
	{
		// NVIDIA
		if ($vendorid == 4318)	
		{		
			return sprintf("%d.%d.%d.%d", 
				($versionraw >> 22) & 0x3ff,
				($versionraw >> 14) & 0x0ff,
				($versionraw >> 6) & 0x0ff,
				($versionraw) & 0x003f
				);
		}
		// Use Vulkan version conventions if vendor mapping is not available
		return sprintf("%d.%d.%d %s", 
			($versionraw >> 22) & 0x3ff,
			($versionraw >> 12) & 0x3ff,
			($versionraw) & 0xfff,
			"<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true' title='The version number conversion scheme for this vendor is not yet available'></span>"
			);
	}
	
	return $versiontext;	
}

?>