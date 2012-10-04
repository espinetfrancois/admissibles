<?php
/**
 * Classes de gestion du Layout
 * @author François Espinet
 * @version 1.0
 *
 */

class Layout {

    /**
     * Contient les donnée <meta> </meta>
     * @var array
     */
    protected $_meta = array();
    
    /**
     * Contient les css à ajouter
     * @var array
     */
    protected $_css = array();
    
    /**
     * Contient les js à ajouter
     * @var unknown_type
     */
    protected $_js = array();

    /**
     * titre de la page
     * @var string
     */
    public $_title = "École Polytechnique - Logement des admissibles";
    /**
     * menu de la page
     * @var string
     */
    protected $_menu = null;
    /**
     * Contenu de la page
     * @var string
     */
    protected $_content = null;

    /**
     * balise doctype
     * @var string
     */
    const doctype = '<!DOCTYPE html>';
    
    const PREPEND    = '1';
    const APPEND    = '2';
    const JS        = '1';
    const CSS        = '2';

    public function __construct() {
        $this->_meta[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $this->appendCss('layout.css');
        $this->addMenu("menu.html");
    }

    public function setTitle($sTitre) {
        $this->_title = $sTitre;
    }

    public function defaultLayout($sContent) {
        $this->_content[] = $sContent;
        return $this->render();
    }

    /**
     * ajoute du contenu à la page
     * @param string $sContent
     */
    public function addContent($sContent) {
        $this->_content[] = $sContent;
    }

    /**
     * Ajout du contenu à la page en le positionnant avant le contenu courant
     * @param String $sContent
     */
    public function prependContent($sContent) {
        array_unshift($this->_content, $sContent);
    }

    /**
     * ajoute le menu à la page
     * @param String $sUrl l'url depuis 'template'
     */
    public function addMenu($sUrl) {
        $this->_menu = '/public/template/'.$sUrl;
    }

    public function addHead($sHead) {
        $this->_meta[] = $sHead;
    }

    public function addWebJs($sUrl) {
        array_unshift($this->_js, '<script type="text/javascript" src="'.$sUrl.'">  </script>');
    }
    /**
     *
     * @param String $sRelUrl l'url relative à partir de public/css/  avec l'extension
     */
    public function appendCss($sRelUrl) {
        $this->_append($sRelUrl, self::CSS);
    }

    /**
     *
     * @param String $sRelUrl l'url relative à partir de public/css/ avec l'extension
     */
    public function prependCss($sRelUrl) {
        $this->_prepend($sRelUrl, self::CSS);
    }

    /**
     *
     * @param String $sRelUrl l'url relative à partir de public/js/ avec l'extension
     */
    public function appendJs($sRelUrl) {
        $this->_append($sRelUrl, self::JS);
    }

    /**
     *
     * @param String $sRelUrl l'url relative à partir de public/js/  avec l'extension
     */
    public function prependJs($sRelUrl) {
        $this->_prepend($sRelUrl, self::JS);
    }

    protected function _append($sElement, $type) {
        $this->__add($sElement, $type);
    }

    protected function _prepend($sElement, $type) {
        $this->__add($sElement, $type, self::PREPEND);
    }

    protected function __add($sElement, $type, $placement = self::APPEND) {
        if ($placement == self::APPEND) {
            if ($type == self::CSS) {
                $this->_css[] = $this->___generateUrl($sElement, $type);
            } elseif ($type == self::JS) {
                $this->_js[] = $this->___generateUrl($sElement, $type);
            } else {
                $this->_meta[] = $sElement;
            }
        } else {
            if ($type == self::CSS) {
                array_unshift($this->_css, $this->___generateUrl($sElement, $type));
            } elseif ($type == self::JS) {
                array_unshift($this->_js, $this->___generateUrl($sElement, $type));
            } else {
                array_unshift($this->_meta, $sElement);
            }
        }
    }

    protected function ___generateUrl($sUrl, $nType) {
        if ($nType == self::JS) {
            return '<script type="text/javascript" src="'.'/public/js/'.$sUrl.'"></script>';
        } else {
            return '<link type="text/css" href="'.'/public/css/'.$sUrl.'" rel="stylesheet" media="all" />';
        }
    }

    public function renderHead() {
        $sHead = '<head>';
        $sHead=$this->renderMeta().$this->renderCss().$this->renderJs();
        return $sHead."<title>".$this->_title."</title>"."\n".'</head>';
    }

    public function renderContent() {
        $sContents = '<div id="page_deco">
                      <div class="contenu" id="contenu">';
        if ($this->_content != null && count($this->_content)) {
            foreach ($this->_content as $sContent) {
                $sContents .= $sContent."\n";
            }
        }
        return $sContents."<br />
        </div>
        </div>";
    }

    public function renderMeta() {
        $sMetas = "";
        foreach ($this->_meta as $sMeta) {
            $sMetas .= $sMeta."\n";
        }
        return $sMetas;
    }

    public function renderJs() {
        $sJs = "";
        if (count($this->_js)) {
            foreach ($this->_js as $saJs) {
                $sJs .= $saJs."\n";
            }
        }
        return $sJs;
    }

    public function renderCss() {
        $sCss = "";
        if (count($this->_css)) {
            foreach ($this->_css as $saCss) {
                $sCss .= $saCss."\n";
            }
            return $sCss;
        }
    }

    public function renderMenu() {
        if ($this->_menu != null) {
            return '<div class= menu>'.file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->_menu).'</div>';
        }
    }
    
    public function renderBandeau() {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'].'/public/template/haut.html');
    }
    
    public function renderPiedPage() {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'].'/public/template/pied_page.html');
    }


    public function render() {
        return self::doctype."\n".'<html>'."\n".$this->renderHead()."\n".$this->renderBandeau().$this->renderMenu()."\n"."<body>\n".$this->renderContent().$this->renderPiedPage()."\n</body>\n"."\n"."</html>";
    }

    public function __toString() {
        return $this->render();
    }


}
?>
