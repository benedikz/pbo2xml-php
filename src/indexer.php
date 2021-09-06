<?php
  /* Name: index.php
   * Description: Automatic mod repository indexing script
   * Author(s): benedi.kz & Zero Launcher
   */

include("defines.php");
include("functions.php");

$source_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER["REQUEST_URI"]) . "";

// Actual rendering of XML
$ID = ID;
$NAME = NAME;

$myfile = fopen("index.xml", "w") or die("Error whilst accessing index.xml");

fwrite($myfile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Repository>\n<Modpacks>\n<Modpack ID=\"#{$ID}\" Name=\"" . NAME . "\" Source=\"{$source_url}\" IP=\"{$AUTOCONNECT_IP}\" Port=\"{$AUTOCONNECT_PORT}\" Password=\"{$AUTOCONNECT_PASSWORD}\" >\n<Addons>\n");

$results = scandir(__DIR__);

foreach ($results as $result)
{
    if ($result === '.' or $result === '..') continue;

    if (is_dir(__DIR__ . '/' . $result))
    {
        fwrite($myfile, "<string>{$result}</string>\n");
    }
}

fwrite($myfile, "</Addons>\n</Modpack>\n</Modpacks>\n<Addons>\n");

for ($i = 0;$i < (count($results) - 1);$i++)
{

    if ($results[$i] === '.' or $results[$i] === '..')
    {
        continue;
    }
    else
    {
        if (startsWith($results[$i], "@")) {
            fwrite($myfile, "<Addon Name=\"" . $results[$i] . "\">\n<Files>\n");

            $it = new RecursiveDirectoryIterator(__DIR__);
            $display = Array(
                'pbo',
                'bisign',
                'bikey',
                'paa',
                'cpp'
            );
            foreach (new RecursiveIteratorIterator($it) as $file)
            {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), $display))
                {
                    if (startsWith(getRelativePath(__FILE__, $file), $results[$i])) {
                        fwrite($myfile, "<File LastChange=\"" . filemtime($file) . "\" Path=\"" . getRelativePath(__FILE__, $file) . "\" Size=\"" . filesize($file) . "\" />\n");
                    }
                }
            }

            fwrite($myfile, "</Files>\n</Addon>\n");
        }
        else
        {
            $i++;
        }
    }
}
fwrite($myfile, "</Addons>\n</Repository>\n");
