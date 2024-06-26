<?php
$echo = true;
$logErrors = true;
$logFileName = "logs/" . date("YmdHis") . "-imageResize.log";

$configHeight = 2880;
$configWidth = 1920;
$dirToResizeImage = "{PATH_TO_DIRECTORY}";

scanAllDirAndResize($dirToResizeImage);

function scanAllDirAndResize($dir)
{
    global $configHeight;
    global $configWidth;

    foreach (scandir($dir) as $filename)
    {
        if ($filename[0] === '.') continue;
        $filePath = $dir . '/' . $filename;
        if (is_dir($filePath))
        {
            scanAllDirAndResize($filePath);
        }
        else
        {
            if(is_file($filePath))
            {
                $fileType = mime_content_type($filePath);
                if(in_array($fileType,array("image/jpeg")))
                {
                    echoAndLog($filePath." - $fileType\n");
                    $ret = imageToBase64($filePath);
                    $encodedBased64 = $ret['dataUrl'];
                    $oriBased64 = $ret['imageData'];
            
                    list($oriWidth, $oriHeight, $type, $attr) = getimagesize($encodedBased64);
                    if (($oriWidth * $oriHeight) > ($configWidth * $configHeight))
                    {
                        $min = min(($configWidth / $oriWidth), ($configHeight / $oriHeight));
                        $oriBased64 = resizeImage($oriBased64, $oriWidth * $min, $oriHeight * $min, $oriWidth, $oriHeight, $type);
                    }
    
                    file_put_contents($filePath, $oriBased64);
                }
            }
        }
    }
}

function echoAndLog($str)
{
    global $echo;
    global $logErrors;
    global $logFileName;

    if (!file_exists('logs')) {
        mkdir('logs', 0777, true);
    }

    if ($echo)
    {
        echo $str;
    }

    if ($logErrors)
    {
        file_put_contents($logFileName, $str, FILE_APPEND | LOCK_EX);
    }
}

//* ----------------------------------------------------------------------------
function imageToBase64($src)
{
    $type = pathinfo($src, PATHINFO_EXTENSION);
    $data = file_get_contents($src);
    $base64 = base64_encode($data);
    $dataUrl = 'data:image/' . $type . ';base64,' . $base64;

    $rets = array();
    $rets['dataUrl'] = $dataUrl;
    $rets['imageData'] = base64_decode(str_replace(" ", "+", $base64));

    return $rets;
}

//* ----------------------------------------------------------------------------
function resizeImage($imgString, $newWidth, $newHeight, $oriWidth, $oriHeight, $type)
{
    $src = imagecreatefromstring($imgString);
    $dst = imagecreatetruecolor(intval($newWidth), intval($newHeight));

    imagecopyresampled($dst, $src, 0, 0, 0, 0, intval($newWidth), intval($newHeight), intval($oriWidth), intval($oriHeight));
    imagedestroy($src);
    ob_start();
    switch ($type)
    {
        case 2:
            imagejpeg($dst);
            break;

        case 3:
            imagepng($dst);
            break;

        default:
            imagepng($dst);
            break;
    }
    $imgString = ob_get_contents();
    ob_end_clean();
    imagedestroy($dst);

    return $imgString;
}

//* ----------------------------------------------------------------------------
function ascii_encode($input)
{
    $output = base64_encode($input);

    $output = str_replace('qO', '~', $output);
    $output = str_replace('wP', '?', $output);
    $output = str_replace('eA', '!', $output);
    $output = str_replace('rS', '@', $output);
    $output = str_replace('tD', '#', $output);
    $output = str_replace('yF', '$', $output);
    $output = str_replace('uG', '%', $output);
    $output = str_replace('iH', '^', $output);
    $output = str_replace('oJ', '&', $output);
    $output = str_replace('pK', '*', $output);
    $output = str_replace('aL', '(', $output);
    $output = str_replace('sZ', ')', $output);
    $output = str_replace('dX', '[', $output);
    $output = str_replace('fC', ']', $output);
    $output = str_replace('gV', '{', $output);
    $output = str_replace('hB', '}', $output);
    $output = str_replace('jN', '<', $output);
    $output = str_replace('kM', '>', $output);
    $output = str_replace('lU', ':', $output);
    $output = str_replace('zY', ';', $output);
    $output = str_replace('xT', '`', $output);
    $output = str_replace('cR', '-', $output);
    $output = str_replace('vE', '_', $output);
    $output = str_replace('bW', '|', $output);
    $output = str_replace('nT', ',', $output);
    $output = str_replace('MR', '.', $output);
    $output = str_replace('pE', '\'', $output);
    $output = str_replace('oW', '"', $output);

    return ($output);
}
