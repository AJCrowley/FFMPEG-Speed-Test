<?php
	$ffBasePath = '/usr/local/Cellar/';
	$ffV2 = $ffBasePath . 'ffmpeg@2.8/2.8.11/bin/ffmpeg';
	$ffV3 = $ffBasePath . 'ffmpeg/3.3.1/bin/ffmpeg';
	$sourceVid = $baseDir . '/source.mp4';
?>
<html>
	<head>
		<title>FFMPEG Speed Test</title>
	</head>
	<style>
		@import url('https://fonts.googleapis.com/css?family=Lato');

		:root {
			--color-ember: #b70e0e;
			--color-silver: #eee;
		}

		body {
			background-color: #ccc;
			margin: 4em auto;
			width: 980px;
		}

		body,
		input {
			font-family: Lato;
		}

		label {
			display: block;
			margin-top: 1em;
		}

		.code {
			background-color: var(--color-silver);
			border-radius: 4px;
			color: var(--color-ember);
			font-family: courier;
			padding: 4px;
		}
	</style>
	<body>
		<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<label>Testing FFMPEG v2: <span class="code"><?=$ffV2?></span></label>
			<label>Vs FFMPEG v3: <span class="code"><?=$ffV3?></span></label>
			<?php
				if(isset($_POST['submit'])) {
					$renders = $_POST['renders'];
					$params = $_POST['params'];
					$delete = isset($_POST['delete']) ? true : false;
					$submitText = 'Re-run speed test';
					$files = [];
					$baseDir = dirname(__FILE__);
					$startTime = time(); ?>
					<label>Running: <span class="code">ffmpeg -i <?=$sourceVid?> <?=$params . ' ' . $baseDir?> output.mp4</span></label>
					<label>Starting v2 encode at: <span class="code"><?=$startTime?></span></label>
					<label>v2 encoding <?=$renders?> times with command: <span class="code"><?=$ffV2 . ' -i ' . $sourceVid . ' ' . $params . ' ' . $baseDir . '/v2_' . $startTime . '.mp4'?></span></label> <?php
					for($v2count = 0; $v2count < $renders; $v2count++) {
						$file = $baseDir . '/v2_' . time() . '.mp4';
						array_push($files, $file);
						shell_exec($ffV2 . ' -i ' . $sourceVid . ' ' . $params . ' ' . $file);
					}
					$endTime = time();
					$duration = round(($endTime - $startTime) / $renders, 2);
					$startTime = time(); ?>
					<label>V2 rendered <span class="code"><?=$renders?></span> times, ended at: <span class="code"><?=$endTime?></span> for average duration of <span class="code"><?=$duration?></span> seconds</label>
					<label>Starting v3 encode at: <span class="code"><?=$startTime?></span></label>
					<label>v3 encoding <?=$renders?> times with command: <span class="code"><?=$ffV3 . ' -i ' . $sourceVid . ' ' . $params . ' ' . $baseDir . '/v3_' . time() . '.mp4'?></span></label> <?php
					for($v3count = 0; $v3count < $renders; $v3count++) {
						$file = $baseDir . '/v3_' . time() . '.mp4';
						array_push($files, $file);
						shell_exec($ffV3 . ' -i ' . $sourceVid . ' ' . $params . ' ' . $file);
					}
					$endTime = time();
					$duration = round(($endTime - $startTime) / $renders, 2); ?>
					<label>V3 rendered <span class="code"><?=$renders?></span> times, ended at: <span class="code"><?=$endTime?></span> for average duration of <span class="code"><?=$duration?></span> seconds</label> <?php
					if($delete) {
						foreach($files as $file) {
							if(file_exists($file)) {
								unlink($file);
							}
						}
					}
				} else {
					$renders = 1;
					$params = '-c:v libx264 -s 1920x1080 -preset fast -profile:v high -level 5.1 -crf 20 -pix_fmt yuv420p -c:a aac -b:a 192k -strict -2';
					$delete = true;
					$submitText = 'Run speed test';
				}
			?>
			<label for="params">Arguments for FFMPEG</label>
			<input type="text" name="params" size="120" value="<?=$params?>">
			<label for="renders">Number of renders to run</label>
			<input type="number" name="renders" min="1" max="50" size="4" value="<?=$renders?>">
			<label for="delete">Delete test renders when finished <input type="checkbox" name="delete" <?=$delete == true ? 'checked' : ''?>></label>
			<input type="submit" name="submit" value="<?=$submitText?>">
		</form>
	</body>
</html>