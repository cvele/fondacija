<?php

namespace AppBundle\Twig;

class DisplayUserExtenstion extends \Twig_Extension
{
    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
           new \Twig_SimpleFilter('display_mention', array($this, 'display_mention')),
        );
    }
    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'display_mention';
    }

    public function display_mention($text)
    {
        preg_match_all("/@\[([A-Za-z0-9\_\-]+)\]\(user\:([0-9]+)\)/i", $text, $matches);

        foreach ($matches[1] as $key => $username) {
            $text = str_replace($matches[0][$key], '<strong class="mention-text">@'.$username.'</strong>', $text);
        }

        return $text;
    }
}
