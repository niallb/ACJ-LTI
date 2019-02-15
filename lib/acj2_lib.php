<?php

class acj2
{
    var $papers;

    function acj2()
    {
        $this->papers = array();
    }

	function recalc1($round)
	{
	    $this->updateRanks();
	    //for($n=0; $n<sizeof($this->papers); $n++)
	    foreach($this->papers as $n=>$pp)
	    {
	        $sc = 0;
	        if(sizeof($this->papers[$n]->comparisons))
	        {
		        foreach($this->papers[$n]->comparisons as $cmp)
		        {
	                if($round <= 4)
	                {
                        //echo "cmp[withID] is {$cmp['withID']}<br/>";
		                $cmprank = $this->papers[$cmp['withID']]->rank;
			            if($cmp['won'])
		                {
			                $sc += 1;
		                }
			            else
		                {
			                $sc -= 1;
		                }
	                }
	                elseif($round <= 8)
	                {
                        //echo "cmp[withID] is {$cmp['withID']}<br/>";
		                $cmprank = $this->papers[$cmp['withID']]->rank;
			            if($cmp['won'])
		                {
			                $sc += $cmprank;
		                }
			            else
		                {
			                $sc -= (sizeof($this->papers)-$cmprank);
		                }
	                }
	                else
	                {
                        //echo "cmp[withID] is {$cmp['withID']}<br/>";
		                $cmprank = $this->papers[$cmp['withID']]->rank;
	                    $thisrank = $this->papers[$n]->rank;
	                    $rmsRankDiff = sqrt(($cmprank-$thisrank)*($cmprank-$thisrank));
			            if($cmp['won'])
		                {
			                $sc += $thisrank + $thisrank/$rmsRankDiff;
		                }
			            else
		                {
			                $sc += $thisrank - $thisrank/$rmsRankDiff;
		                }
	                }
		        }
		        $sc = $sc / sizeof($this->papers[$n]->comparisons);
	        }
	        $this->papers[$n]->addScore($round, $sc);
	    }
	}

	function updateRanks()
	{
	    $rankList = array();
	    foreach($this->papers as $p)
	    {
	        $rankList[] = array('id'=>$p->id, 'score'=>$p->latestScore());
	    }
	    usort($rankList, 'cmp2');
	    for($n = 0; $n<sizeof($rankList); $n++)
	    {
	        $this->papers[$rankList[$n]['id']]->rank = $n;
	    }
	}

	function getPairingsByRank()
	{
	    $this->updateRanks();
	    $targetsList = array();
	    $toPairList = array();
	    $pairedList = array();
	    $pairedCount = 0;
	    foreach($this->papers as $p)
	    {
	        $cws = 0;
	        if(sizeof($p->comparisons)>0)
	        {
		        $n=0;
	            $rank = $p->rank;
	            $scci = 0;
		        foreach($p->comparisons as $c)
		        {
		        	$n++;
	                $compRank = $this->papers[$c['withID']]->rank;
	                $scci += ($rank - $compRank) / (1+sqrt((($rank-$compRank)*($rank-$compRank))));
		        }
	            $cws =  $rank + $scci;
	        }
	        $targetsList[] = array('id'=>$p->id, 'score'=>$cws, 'rank'=>$p->rank, 'targetRank'=>intval(floor($cws+0.5)), 'paired'=>false);
	        $toPairList[] = array('id'=>$p->id, 'score'=>$p->rank, 'targetRank'=>intval(floor($cws+0.5)), 'paired'=>false);
	        $pairedList[$p->id] = false;
	    }
	    usort($targetsList, 'cmp2');
	    usort($toPairList, 'cmp2');
	    $size = sizeof($toPairList);
	    $pairings = array();
	    $nextID = nextAlternating(false, $size);
	    while($nextID !== false)
	    {
	        $pl = $toPairList[$nextID];
	        if($pairedList[$pl['id']]==false)
	        {
	            $pairedList[$pl['id']] = true; // simple way of avoiding matching with it's self;
	            $suggestID = nextOffsetAlternating(false, $pl['targetRank'], $size);
	            while(($suggestID!==false)&&(($pairedList[$targetsList[$suggestID]['id']])||($targetsList[$suggestID]['id']==$pl['id'])||($this->papers[$pl['id']]->countComparisons($targetsList[$suggestID]['id']))))
	            {
	                $suggestID = nextOffsetAlternating($suggestID, $pl['targetRank'], $size);
	            }
	            if($suggestID==false) // try again allowing ones that are already paired
	            {
		            $suggestID = nextOffsetAlternating(false, $pl['targetRank'], $size);
		            while(($suggestID!==false)&&($targetsList[$suggestID]['id']==$pl['id']))
		            {
		                $suggestID = nextOffsetAlternating($suggestID, $pl['targetRank'], $size);
		            }
	            }
	            if($suggestID!==false)
	            {
	                //if($pl['id']==3) echo "Paper rank {$pl['score']}, aiming for {$pl['targetRank']} paired with {$targetsList[$suggestID]['id']} {$pairedList[$targetsList[$suggestID]['id']]} rank {$targetsList[$suggestID]['rank']}, target {$targetsList[$suggestID]['targetRank']}<br/>";
	                $pairedList[$targetsList[$suggestID]['id']] = true;
	                $pairings[] = array($pl['id'],$targetsList[$suggestID]['id']);
	            }
	        }
	        $nextID = nextAlternating($nextID, $size);
	    }
	    return $pairings;
	}
}

class submission
{
    var $id;
    var $perfectScore;
    var $comparisons; // array, 1 or more per round?
    var $acjScores;   // array, 1 per completed round.
    var $rank; // an integer indicating latest ranking.
    var $_latestScore;

    function submission($id)
    {
        $this->id = $id;
        $this->comparisons = array(); // array, 1 or more per round?
        $this->acjScores = array();   // array, 1 per completed round.
        $this->rank = 0;
        $this->_latestScore = 0;
    }

    function addComparison($withID, $won, $round)
    {
        $this->comparisons[] = array('withID'=>$withID, 'won'=>$won, 'round'=>$round);
    }

    function countComparisons($withID)
    {
        $count = 0;
        foreach($this->comparisons as $c)
        {
        	if($c['withID'] == $withID)
                $count++;
        }
        return $count;
    }

    function addScore($round, $score)
    {
        $this->acjScores[$round] = $score;
        $this->_latestScore = $score;
    }

    function latestScore()
    {
        return $this->_latestScore;
    }
}

function cmp($a, $b)
{
    if ($a->latestScore() == $b->latestScore()) {
        return 0;
    }
    return ($a->latestScore() < $b->latestScore()) ? -1 : 1;
}

function cmp2($a, $b)
{
    if ($a['score'] == $b['score']) {
        return 0;
    }
    return ($a['score'] < $b['score']) ? -1 : 1;
}

function nextAlternating($after, $count)
{
    $start = intval(floor(($count / 2)-0.5));
	if($after === false)
        $next = $start;
    else
    {
        $diff = $start - $after;
        if($diff >= 0)
            $next = $start + $diff + 1;
        else
            $next = $start + $diff;
    }
    if(($next >= 0)&&($next < $count))
    	return $next;
    else
        return false;
}

function nextOffsetAlternating($after, $start, $count)
{
    if($start < 0)
        $start = 0;
    if($start >= $count)
        $start = $count-1;
	if($after === false)
        $next = $start;
    else
    {
        $diff = $start - $after;
        if($diff >= 0)
            $next = $start + $diff + 1;
        else
            $next = $start + $diff;
    }
    if($next < 0)
        $next = 2 * $start - $next + 1;
    elseif($next >= $count)
        $next = 2 * $start - $next;

    if(($next >= 0)&&($next < $count))
    	return $next;
    else
        return false;
}


