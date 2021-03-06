<?php
include('link2html.php');

function saveBlock($str, $type)
{
    global $savedBlocks;
    $sz = sizeof($savedBlocks);
    //echo $str;
    $savedBlocks[]=array('type'=>$type, 'content'=>$str);
    return "[--block_".$sz."--]";
}

function wiki2html($wikiText, $preview=false)
{
    global $savedBlocks;
    $savedBlocks = array();
	$out = $wikiText;
    // kill html
	$out = str_replace("<", "&lt;", $out);
    // *nixify line feeds
	$out = str_replace("\r\n", "\n", $out);

/******* fulltext - translations to be performed on the full text **********/

    // Replace fixed blocks ([= to =]) with a marker
    while(preg_match('/\\[=((\\n|.)*?)=]/',$out, $match)>0)
    {
        $out = preg_replace('/\\[=((\\n|.)*?)=]/e',"saveBlock('$1','ext');", $out, 1);
    }

    // Replace preformatted blocks ([@ to @]) with a marker
    while(preg_match('/\\[\\@((\\n|.)*?)\\@]/',$out, $match)>0)
    {
        $out = preg_replace('/\\[\\@((\\n|.)*?)\\@]/e',"saveBlock('$1','pre');", $out, 1);
    }

    // merge lines with a single \ at end
	$out = preg_replace('/([^\\\\])\\\\\\s*$[\\n]/m',"$1 ",$out);

    // replace \\ then line feed with a <br/>
	$out = preg_replace('/([^\\\\])\\\\\\\\\\s*$[\\n]/m',"$1<br/>",$out);

/****** split - conversion of the full markup text into lines to be processed */
    $lines = explode("\n",$out);

    // block markup (headings, paragraphs, lists, pre)

    // pre
    for($n=0; $n<sizeof($lines); $n++)
    {
	    if((strlen(trim($lines[$n]))>1)&&(substr($lines[$n],0,1)==" "))
	    {
	        $lines[$n] = "<:pre>".substr($lines[$n],1);
	    }
    }

	// headings
    for($n=0; $n<sizeof($lines); $n++)
    {
	    $lines[$n] = preg_replace("/^!!!!!!(.*)\\z/", "<:h6>\\1", $lines[$n]);
	    $lines[$n] = preg_replace("/^!!!!!(.*)\\z/", "<:h5>\\1", $lines[$n]);
	    $lines[$n] = preg_replace("/^!!!!(.*)\\z/", "<:h4>\\1", $lines[$n]);
	    $lines[$n] = preg_replace("/^!!!(.*)\\z/", "<:h3>\\1", $lines[$n]);
	    $lines[$n] = preg_replace("/^!!(.*)\\z/", "<:h2>\\1", $lines[$n]);
	    $lines[$n] = preg_replace("/^!(.*)\\z/", "<:h1>\\1", $lines[$n]);
    }

    // lists
    for($n=0; $n<sizeof($lines); $n++)
    {
	    $lines[$n] = preg_replace("/^(\*+)(.*)\\z/", "<:ul \\1>\\2", $lines[$n]);
	    $lines[$n] = preg_replace("/^(#+)(.*)\\z/", "<:ol \\1>\\2", $lines[$n]);
    }

    // hr
    for($n=0; $n<sizeof($lines); $n++)
    {
	    $lines[$n] = preg_replace("/^----+\w*\\z/", "<hr/>", $lines[$n]);
    }

	// Bold, italic and monospaced
    for($n=0; $n<sizeof($lines); $n++)
    {
	    $lines[$n] = preg_replace("/'''(.*?)'''/","<b>$1</b>",$lines[$n]);
	    $lines[$n] = preg_replace("/''(.*?)''/","<i>$1</i>",$lines[$n]);
	    $lines[$n] = preg_replace("/@@(.*?)@@/","<code>$1</code>",$lines[$n]);
	    $lines[$n] = preg_replace("/@@(.*?)@@/","<code>$1</code>",$lines[$n]);
	    $lines[$n] = preg_replace("/@@(.*?)@@/","<code>$1</code>",$lines[$n]);
    }

	// Some other thingies...
    for($n=0; $n<sizeof($lines); $n++)
    {
	    $lines[$n] = preg_replace("/'\^(.*?)\^'/","<sup>$1</sup>",$lines[$n]);
	    $lines[$n] = preg_replace("/'_(.*?)_'/","<sub>$1</sub>",$lines[$n]);
	    $lines[$n] = preg_replace("/{\-(.*?)\-}/","<del>$1</del>",$lines[$n]);
	    $lines[$n] = preg_replace("/{\+(.*?)\+}/","<ins>$1</ins>",$lines[$n]);
	    $lines[$n] = preg_replace("/\[\+\+(.*?)\+\+\]/","<span style='font-size:140%'>$1</span>",$lines[$n]);
	    $lines[$n] = preg_replace("/\[\-\-(.*?)\-\-\]/","<span style='font-size:70%'>$1</span>",$lines[$n]);
	    $lines[$n] = preg_replace("/\[\-(.*?)\-\]/","<span style='font-size:85%'>$1</span>",$lines[$n]);
    }

    // Links
    for($n=0; $n<sizeof($lines); $n++)
    {

    	// references, different func? //$lines[$n] = preg_replace('%\\[\\[@?[a-zA-Z0-9-_#+=~ ./?&:]+\\|#\\]\\]%e',"link2html2('$0', \$preview)", $lines[$n]);
    	$lines[$n] = preg_replace('%\\[\\[@?[a-zA-Z0-9-_#+=~ ./?&:]+(\\|[a-zA-Z0-9-_#+=.~;?\\\\\\/@, ]+)?\\]\\]%e',"link2html2('$0', \$preview)", $lines[$n]);
        $lines[$n] = preg_replace('/([^"\'])\\b((https?|ftp|file):\/\/[-A-Za-z0-9+&@#\/%?=~_|!:,.;]*[-A-Za-z0-9+&@#\/%=~_|])/','$1<a href="$2">$2</a>', $lines[$n]);
	}

	// Images
    for($n=0; $n<sizeof($lines); $n++)
    {
    	//echo "<pre>{$lines[$n]}</pre>";
    	$lines[$n] = preg_replace('/Attach:([A-Za-z0-9-_]+\\.[A-Za-z0-9]+)("[\\w ]+")?/e',"mediaLink('$0', \$preview)", $lines[$n]);
	}

	// Floats, Frames etc.
    for($n=0; $n<sizeof($lines); $n++)
    {
    	$lines[$n] = preg_replace('/%(lfloat|rfloat|lframe|rframe)%(.*)(%%)?/e',"putSpan('$1', '$2')", $lines[$n]);
	}

    //# process blocks...
    $out = "";
    $lastBlock = "";
    $listtypes = "";
    for($n=0; $n<sizeof($lines); $n++)
    {
    	// Prepare to process a line.
    	preg_match('/^<:(h1|h2|h3|h4|h5|h6|ul|ol|pre)\\s*([^>]*)>(.*)/',$lines[$n], $bhd);
	    if(sizeof($bhd)>1)
	    {
	        $lines[$n] = $bhd[3];
            $blocktype = $bhd[1];
        }
        else
        {
        	if(strlen(trim($lines[$n]))>0)
        		$blocktype = "p";
            else
            	$blocktype = "";
        }
        // end paragraphs, pre etc
        if($lastBlock != $blocktype)
        {
        	if(($lastBlock == "p")||($lastBlock == "pre"))
        		$out .= "</$lastBlock>\n";

            if(($blocktype == "p")||($blocktype == "pre"))
            	$out .= "<$blocktype>";
        }

        //echo "Blocktype $blocktype, last was $lastBlock<br>";

        // First process lists
        if(($blocktype=="ul")||($blocktype=="ol"))
        {
	        $cdepth = strlen($bhd[2]);
            $lt = substr($bhd[2],0,1); // # or *
	        $prevdepth = strlen($listtypes);
	        //echo "Depth $cdepth, was $prevdepth<br>";
                $lines[$n] = "<li>".$lines[$n]."</li>";
            if(($prevdepth!=$cdepth)||($blocktype!=$lastBlock))
            {
            	$wdepth = $prevdepth;
                // close off lists 'til a matching type at depth
                while(($wdepth > 0)&&(getLT($listtypes, $wdepth)!=$lt)&&($wdepth>$cdepth-1))
                {
                	$wtype = getLT($listtypes, $wdepth);
                    if($wtype == "*")
                    	$out .= "\n</ul>";
                    else
                    	$out .= "\n</ol>";
                    $wdepth--;
                   // echo "depth now $wdepth<br/>";
                }
                $listtypes = substr($listtypes, 0, $wdepth);
                // build up new list starts
                while($wdepth < $cdepth)
                {
                    if($lt == "*")
                    	$out .= "<ul>";
                    else
                    	$out .= "<ol>";
                    $listtypes .= $lt;
                    $wdepth++;
                }

            }
            $depth = $cdepth;
        }
        else
        {
	        $wdepth = strlen($listtypes);
            while($wdepth > 0)
            {
                $wtype = getLT($listtypes, $wdepth);
                if($wtype == "*")
                    $out .= "\n</ul>";
                else
                    $out .= "\n</ol>";
                $wdepth--;
            }
            $listtypes = "";
        }

	    switch($blocktype)
	    {
        case "ul": // lists prepared above
        case "ol":
        	break;
        case "":
        case "p":
        case "pre":
          	break;
        default:
            $lines[$n] = "<$blocktype>".$lines[$n]."</$blocktype>";
            break;
	    }

	    $lastBlock = $blocktype;
	    $out .= $lines[$n]."\n";
    }

    // Process saved blocks (that weren't processed by extensions - still to work that out...)
    for($n=0; $n<sizeof($savedBlocks); $n++)
    {
    	if($savedBlocks[$n]['type']=='pre')
        {
            $savedBlocks[$n]['content'] = str_replace("\t",'    ',$savedBlocks[$n]['content']);
        	$savedBlocks[$n]['content'] = '<pre>'.$savedBlocks[$n]['content'].'</pre>';
        }
        elseif($savedBlocks[$n]['type']=='ext')
        {
		    // new markup for nbcms [=html= ... =] is a block of preserved html markup.
		    if(substr($savedBlocks[$n]['content'],0,5)=="html=")
		    	$savedBlocks[$n]['content']=html_entity_decode(substr($savedBlocks[$n]['content'],5));
        }
    }

    // Restore fixed blocks ([--block_n--])
    $out = preg_replace('/\\[--block_(\\d+)--]/e',"\$savedBlocks[$1]['content'];", $out);

	return $out;
}

function mediaLink($linkSrc, $preview)
{
	global $cmsGlobals;
	$linkSrc = stripslashes($linkSrc);
	$linkSrc = substr($linkSrc, strpos($linkSrc, ":")+1);
    if(strpos($linkSrc, "\""))
    	list ($linkSrc, $alt, $rem) = explode("\"", $linkSrc, 3);
    else
    	$alt = "";
    if(strpos($linkSrc, "."))
    	$type = substr($linkSrc, strrpos($linkSrc, ".")+1);
    else
    	$type = "";
    if(checkUploadExists($linkSrc))
    {
    	if($preview==true)
        {
	        $subdir = $cmsGlobals['page'];
	        if(strpos($subdir, ".")==false)
	            $subdir = "";
	        else
	            $subdir = substr($subdir, 0, strrpos($subdir, "."))."/";
	        $location = "sites/{$cmsGlobals['dirname']}/uploads/{$subdir}$linkSrc";
        }
        else
        	$location = $linkSrc;
        $info = loadUploadInfo($linkSrc);
        if($alt=="") $alt = $info['alt'];
        if($alt=="") $alt = $linkSrc;
        switch($type)
        {
        case "jpg":
        case "jpeg":
        case "gif":
        case "png":
        	return buildImgTag($location, $alt, $info['width'], $info['height']);
            break;
        case "swf":
        	return buildFlashObject($location, $alt, $info['width'], $info['height']);
            break;
        default:
        	return "<a href='$location'>$alt</a>";
            break;
        }
    }
    else
    {
    	return link2html2("[[Attach:$linkSrc|$alt]]", $preview);
    }
	return $linkSrc;
}

function putSpan($type, $content)
{
/*     .frame
      { border:1px solid #cccccc; padding:4px; background-color:#f9f9f9; }
    .lfloat { float:left; margin-right:0.5em; }
    .rfloat { float:right; margin-left:0.5em; } */
    switch($type)
    {
    	case "rfloat":
        	$style = "float:right; margin-left:0.5em;";
            break;
    	case "lfloat":
        	$style = "float:left; margin-right:0.5em;";
            break;
    	case "rframe":
        	$style = "border:1px solid #888888; padding:4px; float:right; margin-left:0.5em;";
            break;
    	case "lframe":
        	$style = "border:1px solid #888888; padding:4px; float:left; margin-right:0.5em;";
            break;
    }
    return "<span style=\"$style\">" . stripslashes($content) ."</span>";
}

function buildImgTag($location, $alt, $width, $height)
{
    $tag = "<img src=\"$location\" alt=\"$alt\" ";
    if($width > 0)
    	$tag .= "width=\"$width\" ";
    if($height > 0)
    	$tag .= "height=\"$height\" ";
    $tag .= "/>";
    return $tag;
}

function buildFlashObject($location, $alt, $width, $height)
{
	$out="<object";
    if($width != "")
    	$out .= " width='$width'";
    if($height != "")
    	$out .= " height='$height'";
    $out .= "><param name='movie' value='$location'><embed src='$location'";
    if($width != "")
    	$out .= " width='$width'";
    if($height != "")
    	$out .= " height='$height'";
	$out .= "></embed></object>";
    return $out;
}

function getLT($liststr, $depth)
{
    if(strlen($liststr)>=$depth)
	    return substr($liststr, $depth-1, 1);
    else
        return " ";
}


?>
