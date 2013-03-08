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
    public $_title = 'École Polytechnique - Logement des admissibles';
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
     * Propriété true dans le cas ou la page est non-trouvée
     * @var boolean
     */
    public $not_found = false;

    /**
     * variable qui permet d'afficher ou non le menu d'administration
     * false si la personne n'est pas dans l'interface d'administration
     * @var boolean
     */
    public $is_admin = false;

    /**
     * balise doctype
     * @var string
     */
    const doctype = '<!DOCTYPE html>';

    const Prepend    = 1;
    const Append    = 2;
    const Js        = 1;
    const Css        = 2;

    const Menu_Appendice_Admin = 'menu_adminpart.php';

    /**
     * Libraries
     * @var array
     * @access protected
     */
    protected $_libraries = array('jquery/jquery-1.8.2.min.js', 'jquery/jquery-ui-1.8.24.custom.min.js', 'jquery/jquery.visited.js');

    /**
     * Templates
     * @var array
     * @access protected
     */
    protected $_templates = array('jquery/jquery-ui-1.8.24.custom.css');

    /**
     * Constructeur
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_meta[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'."\n".
                '<link href="'.HTTP_IMAGES_PATH.'/favicon.ico" type="image/x-icon" rel="shortcut icon">'."\n".
                '<link href="'.HTTP_IMAGES_PATH.'/favicon.png" type="image/png" rel="icon">'."\n";
        $this->appendCss('layout.css');
        $this->appendCss('forms.css');
        $this->addMenu('menu.html');
        $this->appendJs('menu.js');
        $this->appendCss('images.css');
        $this->appendCss('bonnes_adresses.css');

    }

    /**
     * Assigner le titre
     * @access public
     * @param string $sTitre
     * @return void
     */
    public function setTitle($sTitre)
    {
        $this->_title = $sTitre;

    }

    /**
     * Assigner le layout par défaut
     * @access public
     * @param string $sContent
     * @return string
     */
    public function defaultLayout($sContent)
    {
        $this->_content[] = $sContent;
        return $this->render();

    }

    /**
     * Ajoute une page au layout
     * @access public
     * @param string $sContent
     * @return void
     */
    public function addPage($page)
    {
        ob_start();
        include($page);
        $contents = ob_get_clean();
        $this->_content[] = $contents;

    }

    /**
     * Ajoute du contenu à la page
     * @access public
     * @param string $sContent
     * @return void
     */
    public function addContent($sContent)
    {
        $this->content[] = $sContent;

    }

    /**
     * Ajout du contenu à la page en le positionnant avant le contenu courant
     * @access public
     * @param string $sContent
     * @return void
     */
    public function prependContent($sContent)
    {
        array_unshift($this->_content, $sContent);

    }

    /**
     * ajoute le menu à la page
     * @access public
     * @param string $sUrl l'url depuis 'template'
     * @return void
     */
    public function addMenu($sUrl)
    {
        $this->_menu = $sUrl;

    }

    /**
     * ajoute le head à la page
     * @access public
     * @param string $sHead
     * @return void
     */
    public function addHead($sHead)
    {
        $this->_meta[] = $sHead;

    }

    /**
     * ajoute le javascript à la page
     * @access public
     * @param string $sUrl
     * @return void
     */
    public function addWebJs($sUrl)
    {
        array_unshift($this->_js, '<script type="text/javascript" src="'.$sUrl.'">  </script>');

    }
    /**
     * Append le css
     * @access public
     * @param string $sRelUrl l'url relative à partir de public/css/  avec l'extension
     * @return void
     */
    public function appendCss($sRelUrl)
    {
        $this->_append($sRelUrl, self::Css);

    }

    /**
     * Prepend le css
     * @access public
     * @param string $sRelUrl l'url relative à partir de public/css/ avec l'extension
     * @return void
     */
    public function prependCss($sRelUrl)
    {
        $this->_prepend($sRelUrl, self::Css);

    }

    /**
     * Append le javascript
     * @access public
     * @param string $sRelUrl l'url relative à partir de public/js/ avec l'extension
     * @return void
     */
    public function appendJs($sRelUrl)
    {
        $this->_append($sRelUrl, self::Js);

    }

    /**
     * Prepend le javascript
     * @access public
     * @param string $sRelUrl l'url relative à partir de public/js/  avec l'extension
     * @return void
     */
    public function prependJs($sRelUrl)
    {
        $this->_prepend($sRelUrl, self::Js);

    }

    /**
     * Append un element
     * @access protected
     * @param string $sElement
     * @param int $type
     * @return void
     */
    protected function _append($sElement, $type)
    {
        $this->__add($sElement, $type);

    }

    /**
     * Prepend un element
     * @access protected
     * @param string $sElement
     * @param int $type
     * @return void
     */
    protected function _prepend($sElement, $type)
    {
        $this->__add($sElement, $type, self::Prepend);

    }

    /**
     * Ajoute un element
     * @access protected
     * @param string $sElement
     * @param int $type
     * @param int $placement
     * @return void
     */
    protected function __add($sElement, $type, $placement = self::Append)
    {
        if ($placement == self::Append) {
            if ($type == self::Css) {
                $this->_css[] = $this->___generateUrl($sElement, $type);
            } elseif ($type == self::Js) {
                $this->_js[] = $this->___generateUrl($sElement, $type);
            } else {
                $this->_meta[] = $sElement;
            }
        } else {
            if ($type == self::Css) {
                array_unshift($this->_css, $this->___generateUrl($sElement, $type));
            } elseif ($type == self::Js) {
                array_unshift($this->_js, $this->___generateUrl($sElement, $type));
            } else {
                array_unshift($this->_meta, $sElement);
            }
        }

    }

    /**
     * Génère une url
     * @access protected
     * @param string $sUrl
     * @param int $nType
     * @return string
     */
    protected function ___generateUrl($sUrl, $nType)
    {
        if ($nType == self::Js) {
            return '<script type="text/javascript" src="'.HTTP_JS_PATH.'/'.$sUrl.'"></script>';
        } else {
            return '<link type="text/css" href="'.HTTP_CSS_PATH.'/'.$sUrl.'" rel="stylesheet" media="all" />';
        }

    }

    /**
     * Méthodes de rendu du header
     * @access public
     * @return string
     */
    public function renderHead()
    {
        $sHead = '<head>'."\n";
        $sHead.=$this->renderMeta().$this->renderCss().$this->renderJs();

        return $sHead.'<title>'.$this->_title.'</title>'."\n".'</head>';
    }

    /**
     * Méthodes de rendu du contenu
     * @access public
     * @return string
     */
    public function renderContent()
    {
        $sContents = '<div id="page_deco">
        <div class="contenu" id="contenu">';
        if ($this->_content != null && count($this->_content)) {
            foreach ($this->_content as $sContent) {
                $sContents .= $sContent."\n";
            }
        }

        return $sContents.'<br />
        </div>
        </div>';
    }

    /**
     * Méthodes de rendu des méta
     * @access public
     * @return string
     */
    public function renderMeta()
    {
        $sMetas = '';
        foreach ($this->_meta as $sMeta) {
            $sMetas .= $sMeta."\n";
        }

        return $sMetas;
    }

    /**
     * Méthodes de rendu du javascript
     * @access public
     * @return string
     */
    public function renderJs()
    {
        $sJs = $this->renderLibraries();
        if (count($this->_js)) {
            foreach ($this->_js as $saJs) {
                $sJs .= $saJs."\n";
            }
        }

        return $sJs;
    }

    /**
     * Ajout des bibliothèques définies dans la constant libraries
     * @access protected
     * @return string
     */
    protected function renderLibraries()
    {
        $libraries = '';
        if (count($this->_libraries)) {
            foreach ($this->_libraries as $library) {
                $libraries .= '<script type="text/javascript" src="'.HTTP_LIBRARY_PATH.'/'.$library.'"></script>'."\n";
            }
        }

        return $libraries;
    }

    /**
     * Rendu du template CSS
     * @access protected
     * @return string
     */
    protected function renderCssTemplates()
    {
        $templates = "";
        if (count($this->_templates)) {
            foreach ($this->_templates as $template) {
                $templates .= '<link type="text/css" href="'.HTTP_LIBRARY_PATH.'/'.$template.'" rel="stylesheet" media="all" />'."\n";
            }
        }

        return $templates;
    }

    /**
     * Rendu du CSS
     * @access public
     * @return string
     */
    public function renderCss()
    {
        $sCss = $this->renderCssTemplates();
        if (count($this->_css)) {
            foreach ($this->_css as $saCss) {
                $sCss .= $saCss."\n";
            }
            return $sCss;
        }

    }

    /**
     * Rendu du menu
     * @access public
     * @return string
     */
    public function renderMenu()
    {
        $sMenu =  '';
        if ($this->_menu != null) {
            $sMenu = "\n".'<div class= menu>
                           <ul class="menu_deroulant" id="menu_principal">'.
                           file_get_contents(TEMPLATE_PATH.'/'.$this->_menu);
            if ($this->is_admin || $_SESSION['administrateur'] === true) {
                ob_start();
                include(TEMPLATE_PATH.'/'.self::Menu_Appendice_Admin);
                $sMenu .= ob_get_clean();
                //$sMenu .= file_get_contents(TEMPLATE_PATH.'/menu_adminpart.html');
            }
        }

        return $sMenu.'</ul></div>';
    }

    /**
     * Rendu de l'erreur 404
     * @access public
     * @return string
     */
    public function renderNotFound()
    {
        $sNotFound ="";
        if ($this->not_found) {
            $sNotFound = '<div class="not_found">La page que vous avez demandée n\'a pas été trouvée</div>';
        }

        return $sNotFound;
    }

    /**
     * Rendu du bandeau
     * @access public
     * @return string
     */
    public function renderBandeau()
    {

        return file_get_contents(TEMPLATE_PATH.'/haut.html');
    }

    /**
     * Rendu du pied de page
     * @access public
     * @return string
     */
    public function renderPiedPage()
    {

        return file_get_contents(TEMPLATE_PATH.'/pied_page.html');
    }

    /**
     * Méthode lançant le rendu
     * @access public
     * @return string
     */
    public function render()
    {

        return self::doctype."\n".'<html>'."\n".$this->renderHead().
        "\n<body>".$this->renderBandeau().$this->renderNotFound().$this->renderMenu().
        "\n".$this->renderContent().$this->renderPiedPage().
        "\n</body>\n</html>\n";
    }

    /**
     * Méthode renvoyant le rendu
     * @access public
     * @return string
     */
    public function __toString()
    {

        return $this->render();
    }

}
