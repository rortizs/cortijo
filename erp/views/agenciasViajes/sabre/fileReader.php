<?php

/**
 * @version 1.0
 * Decoder File Sabre
 */
$file = new SplFileObject("./CCZZXO00.PNR");
$i = 1;
while (!$file->eof()) {
    // Echo one line from the file.
    echo $i++ . ' - ' . $file->fgets() . '<br/>';
}
$file = null;
