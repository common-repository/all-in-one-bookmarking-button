<?php

/******************************************************************
Projectname:   Automatic Keyword Generator
Version:       0.3
Author:        Ver Pangonilo <smp_AT_itsp.info>
Last modified: 26 July 2006
Copyright (C): 2006 Ver Pangonilo, All Rights Reserved

* GNU General Public License (Version 2, June 1991)
*
* This program is free software; you can redistribute
* it and/or modify it under the terms of the GNU
* General Public License as published by the Free
* Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will
* be useful, but WITHOUT ANY WARRANTY; without even the
* implied warranty of MERCHANTABILITY or FITNESS FOR A
* PARTICULAR PURPOSE. See the GNU General Public License
* for more details.

Description:
This class can generates automatically META Keywords for your
web pages based on the contents of your articles. This will
eliminate the tedious process of thinking what will be the best
keywords that suits your article. The basis of the keyword
generation is the number of iterations any word or phrase
occured within an article.

This automatic keyword generator will create single words,
two word phrase and three word phrases. Single words will be
filtered from a common words list.

Change Log:
===========
0.2 Ver Pangonilo - 22 July 2005
================================
Added user configurable parameters and commented codes
for easier end user understanding.
						
0.3 Vasilich  (vasilich_AT_grafin.kiev.ua) - 26 July 2006
=========================================================
Added encoding parameter to work with UTF texts, min number 
of the word/phrase occurrences, 

******************************************************************/

class sm_autokeyword {

	//declare variables
	//the site contents
	var $contents;
	//the generated keywords
	var $keywords;
	//minimum word length for inclusion into the single word
	//metakeys
	var $wordLengthMin;
	var $wordOccuredMin;
	//minimum word length for inclusion into the 2 word
	//phrase metakeys
	var $word2WordPhraseLengthMin;
	var $phrase2WordLengthMinOccur;
	//minimum word length for inclusion into the 3 word
	//phrase metakeys
	//minimum phrase length for inclusion into the 2 word
	//phrase metakeys
	var $phrase2WordLengthMin;

	function sm_autokeyword($params)
	{
		//get parameters
		$this->contents = $this->replace_chars($params['content']);

		// single word
		$this->wordLengthMin = $params['min_word_length'];
		$this->wordOccuredMin = $params['min_word_occur'];

		// 2 word phrase
		$this->word2WordPhraseLengthMin = $params['min_2words_length'];
		$this->phrase2WordLengthMin = $params['min_2words_phrase_length'];
		$this->phrase2WordLengthMinOccur = $params['min_2words_phrase_occur'];

	}

	//turn the site contents into an array
	//then replace common html tags.
	function replace_chars($content)
	{
		//convert all characters to lower case
		$content = strtolower($content);
		//$content = mb_strtolower($content, "UTF-8");
		$content = strip_tags($content);

		$punctuations = array(',', ')', '(', '.', "'", "'", '"', '"', "&#96;", "&#145;", "&#146;", "&#8220;", "&#8221;", "&#8222;", "&#8220;", "&#8220;", "&#8222;", '<', '>', '!', '?', '/', '[', ']', ':', '+', '=', '$', '&quot;', '&quot;', '&copy;', '&gt;', '&lt;',
		chr(10), chr(13), chr(9));

		$content = str_replace($punctuations, " ", $content);
		// replace multiple gaps
		$content = preg_replace('/ {2,}/si', " ", $content);

		return $content;
	}

//list of commonly used words
		// this can be edited to suit your needs
		var $common = array("able", "about", "above", "act", "add", "afraid", "after", "again", "against", "age", "ago", "agree", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "amount", "an", "and", "anger", "angry", "animal", "another", "answer", "any", "appear", "apple", "are", "arrive", "arm", "arms", "around", "arrive", "as", "ask", "at", "attempt", "aunt", "away", "back", "bad", "bag", "bay", "be", "became", "because", "become", "been", "before", "began", "begin", "behind", "being", "bell", "belong", "below", "beside", "best", "better", "between", "beyond", "big", "body", "bone", "born", "borrow", "both", "bottom", "box", "boy", "break", "bring", "brought", "bug", "built", "busy", "but", "buy", "by", "call", "came", "can", "cause", "choose", "close", "close", "consider", "come", "consider", "considerable", "contain", "continue", "could", "cry", "cut", "dare", "dark", "deal", "dear", "decide", "deep", "did", "die", "do", "does", "dog", "done", "doubt", "down", "during", "each", "ear", "early", "eat", "effort", "either", "else", "end", "enjoy", "enough", "enter", "even", "ever", "every", "except", "expect", "explain", "fail", "fall", "far", "fat", "favor", "fear", "feel", "feet", "fell", "felt", "few", "fill", "find", "fit", "fly", "follow", "for", "forever", "forget", "from", "front", "gave", "get", "gives", "goes", "gone", "good", "got", "gray", "great", "green", "grew", "grow", "guess", "had", "half", "hang", "happen", "has", "hat", "have", "he", "hear", "heard", "held", "hello", "help", "her", "here", "hers", "high", "hill", "him", "his", "hit", "hold", "hot", "how", "however", "I", "if", "ill", "in", "indeed", "instead", "into", "iron", "is", "it", "its", "just", "keep", "kept", "knew", "know", "known", "late", "least", "led", "left", "lend", "less", "let", "like", "likely", "likr", "lone", "long", "look", "lot", "make", "many", "may", "me", "mean", "met", "might", "mile", "mine", "moon", "more", "most", "move", "much", "must", "my", "near", "nearly", "necessary", "neither", "never", "next", "no", "none", "nor", "not", "note", "nothing", "now", "number", "of", "off", "often", "oh", "on", "once", "only", "or", "other", "ought", "our", "out", "please", "prepare", "probable", "pull", "pure", "push", "put", "raise", "ran", "rather", "reach", "realize", "reply", "require", "rest", "run", "said", "same", "sat", "saw", "say", "see", "seem", "seen", "self", "sell", "sent", "separate", "set", "shall", "she", "should", "side", "sign", "since", "so", "sold", "some", "soon", "sorry", "stay", "step", "stick", "still", "stood", "such", "sudden", "suppose", "take", "taken", "talk", "tall", "tell", "ten", "than", "thank", "that", "the", "their", "them", "then", "there", "therefore", "these", "they", "this", "those", "though", "through", "till", "to", "today", "told", "tomorrow", "too", "took", "tore", "tought", "toward", "tried", "tries", "trust", "try", "turn", "two", "under", "until", "up", "upon", "us", "use", "usual", "various", "verb", "very", "visit", "want", "was", "we", "well", "went", "were", "what", "when", "where", "whether", "which", "while", "white", "who", "whom", "whose", "why", "will", "with", "within", "without", "would", "yes", "yet", "you", "young", "your", "br", "img", "p","lt", "gt", "quot", "copy", "these");

	//single words META KEYWORDS
	function parse_words()
	{
		//create an array out of the site contents
		$x = split(" ", $this->contents);
		//initialize array
		$k1 = array();
		$k2 = array();
		//iterate inside the array
		for ($i=0; $i < count($x); $i++) {
			//delete single or two letter words and
			//Add it to the list if the word is not
			//contained in the common words list.
			if(strlen($x[$i]) >= $this->wordLengthMin  && !$this->common[$x[$i]] && !is_numeric($x[$i])) {
				$k1[$x[$i]]++;
			}		
		}
		
		for ($i=0; $i < count($x)-1; $i++) {
			//delete single or two letter words and
			//Add it to the list if the word is not
			//contained in the common words list.
			if(strlen($x[$i]) >= $this->word2WordPhraseLengthMin  && !$this->common[$x[$i]] && strlen($x[$i+1]) >= $this->word2WordPhraseLengthMin  && !$this->common[$x[$i+1]]) {
				$k2[$x[$i]." ".$x[$i+1]]++; $i+=1;
			}	
		}
		
	
		//sort the words from
		//print_r($k);
		//highest count to the
		//lowest.
		$occur_filtered1 = $this->occure_filter($k1, $this->wordOccuredMin);
		$occur_filtered2 = $this->occure_filter($k2, $this->phrase2WordLengthMinOccur);
		arsort($occur_filtered1);
		arsort($occur_filtered2);

		$nr = 50;
		$imploded .= $this->cut_array($this->implode(", ", $occur_filtered2), $nr);
		$nr = 100 - strlen($imploded);
		$imploded .= $this->cut_array($this->implode(", ", $occur_filtered1), $nr);
		
		unset($k1);
		unset($k2);
		unset($x);

		return substr($imploded,0,-2);
	}
	
	function cut_array($elem, $nr){
		if($elem != "")
			return substr($elem, 0, strrpos(substr($elem, 0, $nr), ",")).", ";
		else return "";
	}
	
	function occure_filter($array_count_values, $min_occur)
	{
		$occur_filtered = array();
		foreach ($array_count_values as $word => $occured) {
			if ($occured >= $min_occur) {
				$occur_filtered[$word] = $occured;
			}
		}

		return $occur_filtered;
	}

	function implode($gule, $array)
	{
		$c = "";
		foreach($array as $key=>$val) {
			@$c .= $key.$gule;
		}
		return $c;
	}
}
?>
