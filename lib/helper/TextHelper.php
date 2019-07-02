<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004 David Heinemeier Hansson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * TextHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @version    SVN: $Id: TextHelper.php 33022 2011-09-15 05:27:12Z fabien $
 */

/**
 * Truncates +text+ to the length of +length+ and replaces the last three characters with the +truncate_string+
 * if the +text+ is longer than +length+.
 */
function truncate_text($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
{
  if ($text == '')
  {
    return '';
  }

  $mbstring = extension_loaded('mbstring');
  if($mbstring)
  {
   $old_encoding = mb_internal_encoding();
   @mb_internal_encoding(mb_detect_encoding($text));
  }
  $strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
  $substr = ($mbstring) ? 'mb_substr' : 'substr';

  if ($strlen($text) > $length)
  {
    $truncate_text = $substr($text, 0, $length - $strlen($truncate_string));
    if ($truncate_lastspace)
    {
      $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
    }
    $text = $truncate_text.$truncate_string;
  }

  if($mbstring)
  {
   @mb_internal_encoding($old_encoding);
  }

  return $text;
}

/**
 * Highlights the +phrase+ where it is found in the +text+ by surrounding it like
 * <strong class="highlight">I'm a highlight phrase</strong>. The highlighter can be specialized by
 * passing +highlighter+ as single-quoted string with \1 where the phrase is supposed to be inserted.
 * N.B.: The +phrase+ is sanitized to include only letters, digits, and spaces before use.
 *
 * @param string $text subject input to preg_replace.
 * @param string $phrase string or array of words to highlight
 * @param string $highlighter regex replacement input to preg_replace.
 *
 * @return string
 */
function highlight_text($text, $phrase, $highlighter = '<strong class="highlight">\\1</strong>')
{
  if (empty($text))
  {
    return '';
  }

  if (empty($phrase))
  {
    return $text;
  }

  if (is_array($phrase) or ($phrase instanceof sfOutputEscaperArrayDecorator))
  {
    foreach ($phrase as $word)
    {
      $pattern[] = '/('.preg_quote($word, '/').')/i';
      $replacement[] = $highlighter;
    }
  }
  else
  {
    $pattern = '/('.preg_quote($phrase, '/').')/i';
    $replacement = $highlighter;
  }

  return preg_replace($pattern, $replacement, $text);
}

/**
 * Extracts an excerpt from the +text+ surrounding the +phrase+ with a number of characters on each side determined
 * by +radius+. If the phrase isn't found, nil is returned. Ex:
 *   excerpt("hello my world", "my", 3) => "...lo my wo..."
 * If +excerpt_space+ is true the text will only be truncated on whitespace, never inbetween words.
 * This might return a smaller radius than specified.
 *   excerpt("hello my world", "my", 3, "...", true) => "... my ..."
 */
function excerpt_text($text, $phrase, $radius = 100, $excerpt_string = '...', $excerpt_space = false)
{
  if ($text == '' || $phrase == '')
  {
    return '';
  }

  $mbstring = extension_loaded('mbstring');
  if($mbstring)
  {
    $old_encoding = mb_internal_encoding();
    @mb_internal_encoding(mb_detect_encoding($text));
  }
  $strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
  $strpos = ($mbstring) ? 'mb_strpos' : 'strpos';
  $strtolower = ($mbstring) ? 'mb_strtolower' : 'strtolower';
  $substr = ($mbstring) ? 'mb_substr' : 'substr';

  $found_pos = $strpos($strtolower($text), $strtolower($phrase));
  $return_string = '';
  if ($found_pos !== false)
  {
    $start_pos = max($found_pos - $radius, 0);
    $end_pos = min($found_pos + $strlen($phrase) + $radius, $strlen($text));
    $excerpt = $substr($text, $start_pos, $end_pos - $start_pos);
    $prefix = ($start_pos > 0) ? $excerpt_string : '';
    $postfix = $end_pos < $strlen($text) ? $excerpt_string : '';

    if ($excerpt_space)
    {
      // only cut off at ends where $exceprt_string is added
      if($prefix)
      {
        $excerpt = preg_replace('/^(\S+)?\s+?/', ' ', $excerpt);
      }
      if($postfix)
      {
        $excerpt = preg_replace('/\s+?(\S+)?$/', ' ', $excerpt);
      }
    }

    $return_string = $prefix.$excerpt.$postfix;
  }

  if($mbstring)
  {
   @mb_internal_encoding($old_encoding);
  }
  return $return_string;
}

/**
 * Word wrap long lines to line_width.
 */
function wrap_text($text, $line_width = 80)
{
  return preg_replace('/(.{1,'.$line_width.'})(\s+|$)/s', "\\1\n", preg_replace("/\n/", "\n\n", $text));
}

/**
 * Returns +text+ transformed into html using very simple formatting rules
 * Surrounds paragraphs with <tt>&lt;p&gt;</tt> tags, and converts line breaks into <tt>&lt;br /&gt;</tt>
 * Two consecutive newlines(<tt>\n\n</tt>) are considered as a paragraph, one newline (<tt>\n</tt>) is
 * considered a linebreak, three or more consecutive newlines are turned into two newlines
 */
function simple_format_text($text, $options = array())
{
  $css = (isset($options['class'])) ? ' class="'.$options['class'].'"' : '';

  $text = sfToolkit::pregtr($text, array("/(\r\n|\r)/"        => "\n",               // lets make them newlines crossplatform
                                         "/\n{2,}/"           => "</p><p$css>"));    // turn two and more newlines into paragraph

  // turn single newline into <br/>
  $text = str_replace("\n", "\n<br />", $text);
  return '<p'.$css.'>'.$text.'</p>'; // wrap the first and last line in paragraphs before we're done
}

/**
 * Turns all links into words, like "<a href="something">else</a>" to "else".
 */
function strip_links_text($text)
{
  return preg_replace('/<a[^>]*>(.*?)<\/a>/s', '\\1', $text);
}

if (!defined('SF_AUTO_LINK_RE'))
{
  define('SF_AUTO_LINK_RE', '~
    (                       # leading text
      <\w+.*?>|             #   leading HTML tag, or
      [^=!:\'"/]|           #   leading punctuation, or
      ^                     #   beginning of line
    )
    (
      (?:https?://)|        # protocol spec, or
      (?:www\.)             # www.*
    )
    (
      [-\w]+                   # subdomain or domain
      (?:\.[-\w]+)*            # remaining subdomains or domain
      (?::\d+)?                # port
      (?:/(?:(?:[\~\w\+%-]|(?:[,.;:][^\s$]))+)?)* # path
      (?:\?[\w\+%&=.;-]+)?     # query string
      (?:\#[\w\-/\?!=]*)?        # trailing anchor
    )
    ([[:punct:]]|\s|<|$)    # trailing text
   ~x');
}
