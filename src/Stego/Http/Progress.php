<?php

namespace Stego\Http;


class Progress
{
    public function show($done, $total)
    {
        $perc = ($done / $total) * 100;
        $bar  = "[" . str_repeat(" ", 100 - $perc);
        $bar  = mb_substr($bar, 0, strlen($bar) - 1) . "🚙 <"; // Change the last = to > for aesthetics
        $bar .= str_repeat("=", $perc) . "] - $perc% - $done/$total";
        echo "\r$bar"; // Note the \r. Put the cursor at the beginning of the line
        ob_flush();
        flush();
    }
}
