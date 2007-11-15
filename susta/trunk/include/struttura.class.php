<?php 
require_once("mytools.inc.php");

class struttura {

    var $_fileStruttura;
    
    var $_content;

    var $_contentStarted;

    /* @var $_images array Contiene le immagini che verrano inserite nella struttura
    */
    var $_images;

    /* @var $_colors array Contiene i colori che verrano inseriti nella struttura
    */
    var $_colors;
    
    /* @var array
    */
    var $_activeMenu;
    
    /* @var $_meta array Contiene i tag meta da aggiungere alla pagina
    */
    var $_meta;
    
    /* @var $_title Titolo della pagina
    */
    var $_title;

    var $_options = array();

    var $_params = array();

    var $_postAdjustPath;

    /* @var array Opzioni varie (controllo 'sottoMenu', 'etc' */
    //var $_menuCode;
    
    /* @var $superStruttura struttura Struttura che contiene quella corrente
    */
//    var $superStruttura;

    function struttura($tplFile = "struttura.inc.php", $contentBegin = true) {
        $this->_fileStruttura = basename($tplFile);
        $this->_meta = array();
        $this->_activeMenu = array();
        $this->_dirStruttura = dirname($tplFile);
        substr($this->_dirStruttura,0,-1) != '/' and $this->_dirStruttura .= '/';
//        echo "<font color=red>$this->_dirStruttura</font>";
        $contentBegin and $this->contentBegin();
    }

    function _struttura() {
        //evid(getcwd());
    }

    /** @desc Imposta un tag meta
    * @param $add bool Se true il contenuto verrà aggiunto al tag meta (se esiste) altrimenti lo rimpiazza (di default)
    */
    function setMeta($what, $content, $add = false) {
        switch ($what) {
            case 'description';
            case 'author':
            case 'keywords': 
                $this->_meta[$what] = "<meta name=\"$what\" content=\"$content\">";
                break;
            default:
                trigger_error("Unknown meta tag", E_USER_WARNING);
        }
    }
    
    function setImage($nome, $file, $attributi = array()) {
        $this->_images[$nome]['src'] = $file;
        list(,,,$attr) = GetImageSize($file);
        isset($attributi['title']) or $attributi['title'] = "";
        isset($attributi['alt']) or $attributi['alt'] = "";
        isset($attributi['border']) or $attributi['border'] = "0";
        foreach ($attributi as $nome => $valore) {
           $valore = htmlentities($valore);
           $attr .= " $nome=\"$valore\"";
        }
        $this->_images[$nome]['attr'] = $attr;

    }
    
    function setColor($nome, $color) {
        $this->_colors[$nome] = $color;
    }
    
    function setTitle($title) {
        $this->_title = "$title";
    }

    function setOption($nome, $value = true) {
        $this->_options[$nome] = $value;
    }
    
    function setParam($nome, $value = true) {
        $this->_params[$nome] = $value;
    }
    
    function setActiveMenu($name) {
        $this->_activeMenu[] = $name;
    }
    
    function setActiveLI($nr, $set = 0) {
		$this->_activeLI[$set][] = $nr;
    }
    
    function putContent($content) {
        $this->_content .= $content;
    }
    
    function display($pathRelocator = null) {
        $pathRelocator and $this->_postAdjustPath = $pathRelocator;
        $this->_postAdjustPath or $this->_postAdjustPath = '.';
/*        // compongo il tutto :)
*/
        $this->_contentStarted and $this->contentEnd();
        if (!$this->_content) {
           trigger_error("Nessun contenuto da visualizzare", E_USER_WARNING);
        }
        $contenuto = $this->_content;
        $this->_content = "<divisore />";
        //print_r($this);

        if (is_array($this->stylesheets)) {
           foreach ($this->stylesheets as $i => $file) {
              $this->stylesheets[$i] = "@$file@";
           }
        }

        $p1 = $this->_dirStruttura;
        $p2 = $this->_postAdjustPath;
        $p1 .= $p1{strlen($p1)-1} == '/' ? '' : '/';
        $p2 .= $p2{strlen($p2)-1} == '/' ? '' : '/';
        $p1p2 = "$p1$p2";


       /* evid(array("getcwd" => getcwd(),
                   "nomeFile" => $this->_fileStruttura,
                   "dirFile" => $this->_dirStruttura)); /**/

        $curDir = getcwd();
        chdir($this->_dirStruttura);
        ob_start();
        include $this->_fileStruttura;
        $pagina = ob_get_clean();
        chdir($curDir);

        require_once("tag.class.php");
        $m = new piece_of_text($pagina);
        $tag = new tag($m);

        $tagDivisore = $tag->find_opening_tag("divisore", array(), TAG_CONTEXT_FULL);


        $pre  = $this->_adjustPaths($tagDivisore->preceding_text(), $this->_postAdjustPath ? $p1p2 : $p2);
        $in   = $this->_adjustPaths($contenuto,                     $this->_postAdjustPath ? $p2   : '.');
        $post = $this->_adjustPaths($tagDivisore->following_text(), $this->_postAdjustPath ? $p1p2 : $p2);

        print($pre . $in . $post);


        return;

/*
        // Imposto il titolo in automatico
        require_once("tag.class.php");
        $tag = new tag(new piece_of_text($pagina));
        $tagImg = $tag->find_tag("title", array(), TAG_CONTEXT_FULL);
        $title = htmlentities($this->_title);
//        $tagImg->pD();
//        evid($tagImg);
        if (!$tagImg->contenuto->is_empty()) $title .= $tagImg->contenuto->get();
        $menuCode = $tagImg->preceding_text() . $title  . $tagImg->following_text();
        echo $pagina;
*/
    }

    /**
    * @param $area string
    * @return string
    */
    function _adjustPaths($area, $dir) {

//$dir = './pippo/.././ciao';
//        evid($dir."-");
/*        $i = 1000;*/

        $dir{strlen($dir)-1} != '/' and $dir .= '/';

        // pulisco cartella ../
        // METTERE UN ASSERTION NELLA REGULAR EXPRESSION!!
        //trigger_error("Scrivi l'assertion");
        while (/*$i-- and*/ $dir != ($dir = preg_replace("/(?!\.\.\/)(\.\.\/)/", "", $dir)));

        // pulisco cartella ./
        while ($dir != ($dir = preg_replace("/([^.])(\.\/)([^\/]*)/", "\\1\\3", $dir)));
//        evid($dir); echo "<hr>";
        $dir or $dir = './';
        if ($dir == './') return $area;

        // search and replace...
        $i = 0;
        $casi[++$i]['nome'] = "href";
        $casi[$i]['expr']   = "/(href[^=]*[^\"]*.)([^'\"]*)(\")/";
        $casi[$i]['sost']['set'] = array(2);

        $casi[++$i]['nome'] = "src";
        $casi[$i]['expr']   = "/(src[^=]*[^\"]*.)([^'\"]*)(\")/";
        $casi[$i]['sost']['set'] = array(2);

        $casi[++$i]['nome'] = "background";
        $casi[$i]['expr']   = "/(background[^=]*[^\"]*.)([^'\"]*)(\")/";
        $casi[$i]['sost']['set'] = array(2);

        $casi[++$i]['nome'] = "action";
        $casi[$i]['expr']   = "/(action[^=]*[^\"]*.)([^'\"]*)(\")/";
        $casi[$i]['sost']['set'] = array(2);

        $casi[++$i]['nome'] = "MM_swapImage";
        $casi[$i]['expr']   = "/MM_swapImage[^'\"]*'([^']*)'[^']*'[^']*'[^']*'([^']*)/";
        $casi[$i]['sost']['set'] = array(2);

        $casi[++$i]['nome'] = "MM_preloadImages";
        $casi[$i]['expr']   = "/MM_preloadImages[^'\"]*'([^']*)'[^']*'([^']*)'[^']*'([^']*)/";
        $casi[$i]['sost']['set'] = array(1, 2, 3);

        $casi[++$i]['nome'] = "css";
        $casi[$i]['expr']   = "/url\([\"']?([^)]*)/";
        $casi[$i]['sost']['set'] = array(1);


        foreach ($casi as $oggetto) {
           $risultato = preg_match_all($oggetto['expr'], $area, $trovati, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) == 0;
           if (count($trovati)) {
              $precPath = null;
              $rewrittenArea = "";
              foreach ($trovati as $paths)
                 foreach ($oggetto['sost']['set'] as $setNr) {
                    $path = $paths[$setNr];
                    // salto path da non toccare
                    $rewrittenArea .= substr($area,
                                              $start = is_null($precPath) ? 0 : ($precPath[1] + strlen($precPath[0])),
                                              $path[1]-$start);
                     if (!preg_match("/(^\/|\w+:\/\/|mailto:|@[^@]*@)/", $path[0])) {
                        $rewrittenArea .= $dir . $path[0];
                     } else {
                        if (preg_match("/@([^@]*)@/", $path[0])) {
                           $path[0] = substr($path[0], 1, -1);
                           $path[1] += 2;   // hack per l'offset nel buffer di testo
                        }
                        $rewrittenArea .= $path[0];
                     }
                    $precPath = $path;
                 }
              $area = $rewrittenArea . substr($area, $precPath[1]+strlen($precPath[0]));
           }
        }
        return $area;
    }

    function setNome($nome) {
        @$oldVal = $this->_options['nome'];
        $this->_options['nome'] = $nome;
        $this->_nome = $nome;
        return $oldVal;
    }
    
    function printNome() {
        echo $this->_options['nome'];
    }
    
    function getOption($option) {
        return $this->_options[$option];
    }
    
    function printMeta() {
        foreach ($this->_meta as $tag) {
            echo $tag;
        }
    }
    
    function printTitle() {
        echo htmlentities($this->_title);
    }
    
    function printColor($color) {
        echo $this->_colors[$color];
    }

    function printImage($image) {
//        evid($this->_images[$image]);
        list ($src,$attr) = $this->_images[$image];
        echo "src=\"{$this->_images[$image]['src']}\" {$this->_images[$image]['attr']}";
    }
    
    function printContent() {
        echo $this->_content;
    }
    
    function contentBegin() {
        if ($this->_contentStarted) {
            trigger_error("contentBegin() già eseguita.", E_USER_WARNING);
            return ;
        }
        ob_start();
        $this->_contentStarted = true;
    }
    
    function contentEnd() {
        if (!$this->_contentStarted) {
            trigger_error("contentBegin() non eseguita.", E_USER_WARNING);
            return ;
        }
        $this->putContent(ob_get_clean());
        $this->_contentStarted = false;
    }
    
    function _menuBegin() {
        ob_start();
    }
    
    function _menuEnd() {
        $menuCode = ob_get_clean();
        require_once("tag.class.php");
        $m = new piece_of_text($menuCode);
        foreach ($this->_activeMenu as $tagName) {
            $tag = new tag($m);
            $tagImg = $tag->find_tag("img", array("name" => "$tagName"), TAG_CONTEXT_FULL);
            $tagLink = $tagImg->find_tag("a", null, TAG_CONTEXT_BEFORE);
            $onMouseOver = $tagLink->apertura->regex_match("/\([^)].*$tagName.*\)/");
//          $onMouseOver->pD();
            $imgOn = $onMouseOver->regex_match("/'([^']*)'/", 3, 1);
            // imposto l'src del tag
            $newtag = preg_replace("/(src=\")([^'\"]*)(\")/", "\\1" . $imgOn->get() . "\\3", $tagImg->get());
            $menuCode = $tagImg->preceding_text() . $newtag . $tagImg->following_text();
        }
        echo $menuCode;     
    }
    
    function _listBegin() {
        ob_start();
    }
    
    function _listEnd($attrForCur, $set = 0) {
//		$attrForCur .= " $set";
        $htmlCode = ob_get_clean();
//        trigger_error("",E_USER_ERROR);
        require_once("tag.class.php");
        $m = new piece_of_text($htmlCode);

        $tag = new tag($m);
        $curtag = $tag->find_tag("li", array(), TAG_CONTEXT_FULL);
		$gli_ul = $tag->find_tag("ul", array(), TAG_CONTEXT_FULL);
		$gli_ul = $gli_ul->find_tag("ul", array(), TAG_CONTEXT_INSIDE);
//		$gli_ul->pD();
        $i = 1;
        while ( !$curtag->is_empty() ) {
           if (!is_array($this->_activeLI[$set]) or array_search($i, $this->_activeLI[$set]) !== false) {
            	$newtag_opening = preg_replace( "/(<li)([^>]*>)/", "\\1 " . $attrForCur . "\\2", $curtag->apertura->get() );
            	$htmlCode = $curtag->preceding_text() . $newtag_opening . $curtag->contenuto->get() . $curtag->chiusura->get() . $curtag->following_text();
           }
		   do {
				$curtag = $curtag->find_tag( "li", array(), TAG_CONTEXT_AFTER );
				//$curtag->pD(); echo "$set " . ($gli_ul->contains( $curtag ) ? 'vero' : 'falso');
           } while ( $gli_ul->contains( $curtag ) ); 
		   
		   $i++;
           if (!is_array($this->_activeLI[$set])) break;
           if ($i==50) break;	// check paranoia anti-loop
        }
        echo $htmlCode;
    }
    
	function _listEndA($attrForCur, $set = 0) {
        $htmlCode = ob_get_clean();
//        trigger_error("",E_USER_ERROR);
        require_once("tag.class.php");
        $m = new piece_of_text($htmlCode);

        $tag = new tag($m);
        $curtag = $tag->find_tag("a", array(), TAG_CONTEXT_FULL);
        $i = 1;
        while (!$curtag->is_empty()) {
           if (!is_array($this->_activeLI[$set]) or array_search($i, $this->_activeLI[$set]) !== false) {
            $newtag = preg_replace("/(<a)([^>]*>)/", "\\1 " . $attrForCur . "\\2", $curtag->get());
            $htmlCode = $curtag->preceding_text() . $newtag . $curtag->following_text();
           }
           $curtag = $curtag->find_tag("a", array(), TAG_CONTEXT_AFTER);
/*		   $enctag = $curtag->enclosing_tag('ul');
		   if (!$enctag->is_empty()) {	// saltiamo le sotto liste
		    	$enctag = $curtag->enclosing_tag('li');
				if (!$enctag->is_empty()) { $curtag = $curtag->find_tag("li", array(), TAG_CONTEXT_AFTER); }
		   }*/
           $i++;
           if (!is_array($this->_activeLI[$set])) break;
           if ($i==50) break;	// check paranoia anti-loop
        }
        echo $htmlCode;
    }
    
    function addStylesheets($files) {
      !is_array($files) and $files = array($files);
      $this->stylesheets = array_merge($this->stylesheets, $files);
    }

	function printStylesheets() {
		if (is_array($this->stylesheets))
			foreach ($this->stylesheets as $file) {
?>
<link rel="stylesheet" href="<?php echo $file; ?>" type="text/css">
<?php
		}	
	}
	
    /* STATICO */
    function scriptNameIs( $nome ) {
      return strpos( $_SERVER['SCRIPT_NAME'], $nome ) !== false;
    }

}
?>