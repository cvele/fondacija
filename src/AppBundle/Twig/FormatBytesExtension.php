<?php

namespace AppBundle\Twig;

class FormatBytesExtension extends \Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
           new \Twig_SimpleFilter('format_bytes', array($this, 'format_bytes')),
        );
    }
    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'format_bytes';
    }
    /**
     * Filter for converting bytes to a human-readable format, as Unix command "ls -h" does.
     *
     * @param string|int $bytes           A string or integer number value to format.
     * @param bool       $base2conversion Defines if the conversion has to be strictly performed as binary values or
     *                                    by using a decimal conversion such as 1 KByte = 1000 Bytes.
     *
     * @return string The number converted to human readable representation.
     * @todo: Use Intl-based translations to deal with "11.4" conversion to "11,4" value
     */
    public function format_bytes($bytes, $base2conversion = true)
    {
        if (!is_numeric($bytes)) {
            return;
        }
        $unit = $base2conversion ? 1024 : 1000;
        if ($bytes < $unit) {
            return $bytes.' B';
        }
        $exp = intval((log($bytes) / log($unit)));
        $pre = ($base2conversion ? 'kMGTPE' : 'KMGTPE');
        $pre = $pre[$exp - 1].($base2conversion ? '' : 'i');
        return sprintf('%.1f %sB', $bytes / pow($unit, $exp), $pre);
    }
}
