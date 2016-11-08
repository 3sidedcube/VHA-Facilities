<?php
	require_once("Files.php");
	require_once("Dsv.php");

	function dump($object)
	{
		if ($object === true)
		{
			$object = "true (boolean)";
		}
		elseif ($object === false)
		{
			$object = "false (boolean)";
		}
		elseif ($object === null)
		{
			$object = "null";
		}

		echo("<pre class=\"dump\">");
		print_r($object);
		echo("</pre>");
	}

	function clean($dirty)
	{
		$dirty = trim($dirty);

		// See http://uk3.php.net/manual/pl/regexp.reference.unicode.php for list of supported scripts.
		$characterSets = array("Arabic", "Hebrew", "Han", "Tagalog");

		if (mb_check_encoding($dirty, "UTF-8") === false)
		{
			$dirty = mb_convert_encoding($dirty, "UTF-8");
		}

		if (preg_match("/(\p{" . implode("}|\p{", $characterSets) . "})+/u", $dirty) == 0)
		{
			$good = array("'", "'", '"', '"', '-', '-', '...');

			$bad = array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6");
			$clean = str_replace($bad, $good, $dirty);

			#$bad = array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133));
			#$clean = str_replace($bad, $good, $clean);
		}
		else
		{
			$clean = $dirty;
		}

		return $clean;
	}


	$dsv = new Dsv();
	$dsv->load("master.csv");


	$rows = $dsv->toStructure();

	$results = array();

	foreach ($rows as $row)
	{
		$current = (object)array(
			"name" => clean($row['Name of Office']),
			"city" => clean($row['City']),
			"type" => "Red Cross Chapters",
			"state" => clean($row['State']),
			"postal_code" => clean($row['Zip Code']),
			"latitude" => clean($row['Latitude']),
			"longitude" => clean($row['Longitude']),
			"url" => clean($row['Website URL']),
			"phone" => clean($row['Contact Phone Number'])
		);

		foreach ($current as &$field)
		{
			if ($field === "")
			{
				$field = null;
			}
		}

		$results[] = $current;
	}

	echo(json_encode($results, JSON_PRETTY_PRINT) . "\n");
?>
