<!DOCTYPE html>
<!--

    Author:        Twily                                        2016
    Website:       http://twily.info/
    Compatibility: Mozilla Firefox, Internet Explorer, Google Chrome

-->
<html>
<head>
<title>Twily Upload</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<!--<link id="favicon" rel="shortcut icon" href="http://twily.info/favicon.ico" />-->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<link rel="stylesheet" type="text/css" href="./css/main.css" />
<script type="text/javascript" src="./js/file.js"></script>
<script type="text/javascript" src="./js/text.js"></script>
</head>
<body>

<div class="tbl">
	<div class="tbl-tr">
		<div class="tbl-td left">

			<div class="tbl">
				<div class="tbl-tr">
					<div class="tbl-td stretch">
						<input type="file" name="file" id="file" />
					</div>
				</div>
				<div class="tbl-tr">
					<div class="tbl-td fr">

						<div class="tbl">
							<div class="tbl-td stretch">
								<span id="bar"><span id="progress">0%</span><span id="note">(Max 256M)</span></span>
							</div>
							<div class="tbl-td">
								<input type="button" value="Upload file" onclick="upload();" />
							</div>
						</div>

					</div>
				</div>
			</div>

		</div>

		<div class="tbl-td sep"></div>

		<div class="tbl-td right">

			<div class="tbl">
				<div class="tbl-tr">
					<div class="tbl-td">
						<textarea placeholder="Text to upload (Max 4096)" id="textA"></textarea>
					</div>
				</div>
				<div class="tbl-tr">
					<div class="tbl-td fr">
						<input type="button" value="Upload text" onclick="post();" />
					</div>
				</div>
			</div>

		</div>
	</div>
	
	<div class="tbl-tr sep"></div>

	<div class="tbl-tr">
		<div class="tbl-td content">
			<iframe src="./list.php" id="list"></iframe>
		</div>

		<div class="tbl-td sep"></div>

		<div class="tbl-td content">
			<iframe src="./text.php" id="text"></iframe>
		</div>
	</div>
</div>

</body>
</html>


