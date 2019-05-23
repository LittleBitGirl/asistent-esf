<?php
class Normalization{

	public $problemsList = [];
	public $sozdik_words;
	public function Classificator($word){
		global $problemsList;
		global $sozdik_words;
		$this->caseProblem($word);
		$vow = $this->isVowelFrequency($word);
		$cons = $this->isConsonantFrequency($word);
		$this->noVowels($word);
		$abbr = 0;
        $sozdik_words = file('words.txt', FILE_IGNORE_NEW_LINES);
        var_dump($problemsList);
		if($this->isNotWord($word) == true){
			foreach ($problemsList as $key => $value){
				switch ($problemsList[$key]) {
					case 'mixed':
						if($this->isCapitalized($word)){
							$word = mb_strtolower($word);
							//$word = ucfirst($word);
						} else {
							$word = mb_strtolower($word);
						}
						break;
					case 'upper':
						if($this->isAbbreviation($word) == false){
							$word = mb_strtolower($word);
						}
						else{
							$abbr = 1;
						}
						break;
					case 'vowelsRepeat':
						$word = $this->str_replace_first($vow, mb_substr($vow, 0, 1), $word);
						break;
					case 'consonantRepeat':
					    // $matchC = mb_substr($matchC, 0, -1);
						$word = $this->str_replace_first($cons, mb_substr($cons, 0, 1), $word);
						break;
					case 'noVowels':
						//array_push($problemsList, 'noVowels');
					default:
						// echo $word.' is normal';
						
						break;
				}
			}
			foreach ($sozdik_words as $key => $value) {
				die($word);
				if($sozdik_words[$key] == $word){
					
					$abbr = 1;
					break;
				}
				else
					$abbr = 0;	
			}
		}
		echo $abbr;
		if($abbr == 0){
			$spell = new LevenshteinCorrector();
			echo $spell->match($word);
		} else{
			echo $word;
		}	
	}

	public function caseProblem($word){
		global $problemsList;
		if(mb_strtolower($word) === $word)
			$m=1;
		else if (mb_strtoupper($word) === $word)
			$problemsList[] = 'upper';
		else
			$problemsList[] = 'mixed';
	}
	public function isNotWord($word){
		if(ctype_alpha($word))
			return false;
		else
			return true;
	}
	public function isEmoticon($word){
		global $problemsList;
		$emoticons = file('emoticons.txt', FILE_IGNORE_NEW_LINES);
		foreach ($emoticons as $key => $value) {
			if($emoticons[$key] = $word)
				$problemsList[] = 'emoticon';
			else{

			}
		}
	}
	public function hashOrMention($word){
		global $problemsList;
		if(strpos($word,'#') !== false)
			$problemsList[] = 'hashtag';
		else if(strpos($word, '@') !== false)
			$problemsList[] = 'mention';
		else{

		}
	}
	public function isVowelFrequency($word){
		global $problemsList;
		preg_match_all('([АаӘәІіҮүҰұӨөОоЕеИиЯяЫыЭэЮюИиУуЁё]{2,})', $word, $matches);
		foreach ($matches as $key => $value) {
			$match = array_shift($value);
			if(mb_strlen(array_shift($value)) >= 2){
				$problemsList[] = 'vowelsRepeat';
				echo $match;
				return $match;
			}
			else
				return 'noRep';
		}
	}
	public function isConsonantFrequency($word){
		global $problemsList;
		preg_match_all('([БбВвГгҒғДдЖжЗзКкҚқЛлМмНнҢңПпРрСсТтФфХхҺһЦцЧчШшЩщЪъЬь]{3,})', $word, $matches);
		foreach ($matches as $key => $value) {
			if(strlen(array_shift($value))>=3){
				$problemsList[] = 'consonantRepeat';
				return array_shift($value);
			}
			else
				return 'noRep';
		}
	}
	public function noVowels($word){
		global $problemsList;
		if(substr_count($word, '[АаӘәІіҮүҰұӨөОоЕеИиЯяЫыЭэЮюИиУуЁё]') !== 0)
			$problemsList[] = 'noVowels';
		else{
			return;
		}
	}
	public function isCapitalized($word) {
    	$string = mb_substr ($word, 0, 1, "UTF-8");
    	return mb_strtolower($string, "UTF-8") != $string;
	}
	public function isAbbreviation($word) {
		$abbreviations = file('abbreviation.txt', FILE_IGNORE_NEW_LINES);
		foreach ($abbreviations as $key => $value) {
			if($abbreviations[$key] == $word)
				return true;
			else
				return false;
		}
	}
	
	public function str_replace_first($from, $to, $content){
	    $from = '/'.preg_quote($from, '/').'/';

	    return preg_replace($from, $to, $content, 1);
	}
}
class Corrector{
    protected $possibleTokens = array();
    
    public function __construct(array $possibleTokens = array())
    {
        $this->possibleTokens = $possibleTokens;
    }

    public function correct($input) {
        $guess = $this->match($input);
        if ($guess === $input)
            return $guess;
        else
            return $this->askForGuess($guess) ? $guess : $input;
    }

    public function match($input)
    {
        if (empty($this->possibleTokens))
            return $input;
        $bestSimilarity = -1;
        $bestGuess = $input;
        foreach ($this->possibleTokens as $possibleToken) {
            similar_text($input, $possibleToken, $similarity);
            if ($similarity > $bestSimilarity) {
                $bestSimilarity = $similarity;
                $bestGuess = $possibleToken;
            }
        }
        return $bestGuess;
    }
    private function askForGuess($guess)
    {
        $prompter = new Prompter;
        $answer = $prompter->ask("Did you mean '$guess'?", array('Y','n'), 'Y');
        return !$answer || strtolower($answer) == 'y';
    }
}

class LevenshteinCorrector extends Corrector
{
    
    public function match($input)
    {

        $sozdik_words = file('words.txt', FILE_IGNORE_NEW_LINES);
        $shortest = -1;
       
        //$sozdik = $sozdik_words;
        $queue = new SplPriorityQueue();

                
        foreach ($sozdik_words as &$word) {             
            $lev = levenshtein($input, $word);
            $str = '';                      
            if ($lev == 0){               
                $closest = $word;                 
                $shortest = 0;
                break;             
            }
            if($lev <= $shortest || $shortest < 0) { 
                $closest = $word;
                $shortest = $lev;                 
                $queue->insert($word, $lev);           
            }
 
        }
        return $closest;     
    } 
}


$normalizer = new Normalization();
// $normalizer -> Classificator("КӨӨӨӨккктЕМ");
// $normalizer -> Classificator("КИМЭБИ");
$normalizer -> Classificator("маааалллл");
?>