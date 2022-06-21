<?php

declare(strict_types=1);

/**
 * Simple and smart pagination builder.
 * @author Inacio Agostinho Uassire
 */

namespace sprint\spagination;

/**
 * Class SPagination
 * @package sprint\spagination
 */
class SPagination
{
    /**
     * @var array
     */
    private static $data = [];
    /**
     * @var array
     */
    private static $anchors     = [];
    /**
     * array that contains list of links
     * @var array
     */
    private static $lists       = [];

    /**
     * first and last labels
     * @var array
     */
    private static $pageHttpLabel = "page";

    /**
     * first and last labels
     * @var array
     */
    private static $firstAndLastLabel = ["First Page","Last Page"];
    /**
     * previous and next labels
     * @var array
     */
    private static $previousAndNextLabel = ["Previous","Next"];
    /**
     * either show dots in the pagination or not
     * @var bool
     */
    public static $dots         = false;
    /**
     * either show the links in the list item or in div
     * @var bool
     */
    public static $asUl         = true;
    /**
     * total filtered rows from the sql query
     * @var
     */
    public static $total;
    /**
     * number of links to show in the pagination in the right and left side based on the current active page
     * @var int
     */
    public static $links        = 4;
    /**
     * sql query limit
     * @var int
     */
    public static $limit;
    /**
     * pagination number, retrieved via http request
     * @var int
     */
    public static $page;
    /**
     * sql query offset, dynamically generated via the self::$limit and the current self::$page
     * @var int
     */
    public static $offset;

    /**
     * Url to set pagination, if is empty then ?page is called
     * @var
     */
    public static $url;
    /**
     * Array containing style of each html element in pagination
     * @var array
     */
    public static $classes      = array(
                                    "a" => array(
                                        "class" => ["page-link"],
                                        "active"=> ["active", "disabled"],
                                    ),
                                    "li" => array(
                                        "class" => ["page-item"],
                                        "active"=> [""],
                                    ),
                                    "ul" => array(
                                        "class" => ["pagination"],
                                    ),
                                    "div" => array(
                                        "class" => [],
                                    ),
                                );
    /**
     * controls the visibility of pagination parts either the numbers is shown or not, previous or next, first or last
     * @var array
     */
    public static $visible = array(
        "pageNumbers"           => true,
        "pagePreviousAndNext"   => true,
        "pageFirstAndLast"      => true
    );

    /**
     * constructor
     * @param $page - current page number retrieved from the http request
     * @param $limit - number of results displayed per page
     * @return no return
     */
    public function __construct($page = 1, $limit = 12){
        self::$page     = $page;
        self::$limit    = $limit;
        self::$offset = (self::$page * self::$limit) - self::$limit;
    }

    /**
     * Builds the pagination
     * @return string - returns the pagination
     */
    public static function page()
    {
        $class = self::getClassValue("a", "class");
		
		$active = array_merge($class["class"], self::getClassValue("a", "active")["active"]);

        if (self::$total > self::$limit):
        
            $totalPages = ceil(self::$total / self::$limit);
        
            if(self::$visible["pageFirstAndLast"] === true)
                self::a(1, self::$firstAndLastLabel[0] , $class["class"]);

            if(self::$visible["pagePreviousAndNext"] === true)
                self::a(((self::$page == 1) ? 1 : self::$page - 1), self::$previousAndNextLabel[0] , $class["class"]);
        
            if(self::$visible["pageNumbers"] === true)
            {
                $start  = self::$page - self::$links;
                $end    = self::$page + self::$links;

                if(self::$dots === false)
                {
                    for ($i = $start; $i <= self::$page - 1; $i++):
                        if ($i >= 1):
                            self::a($i, $i, $class["class"]);
                        endif;
                    endfor;
                
                    self::a(self::$page, self::$page, ((self::$page == $i) ? $active : $class["class"]));
                
                    for ($i = self::$page + 1; $i <= $end; $i++):
                        if ($i <= $totalPages):
                            self::a($i, $i, $class["class"]);
                        endif;
                    endfor;
                }else{
                    $start = $start > 0 ? $start : 1;
                    $end   = $end < $totalPages ? $end : $totalPages;

                    if($start > 1)
                    {
                        self::a(1, 1, $class["class"]);
                        self::a(null, "...", $class["class"]);
                    }

                    for ($i = $start; $i <= $end; $i++):
                        self::a($i, $i, ((self::$page == $i) ? $active : $class["class"]));
                    endfor;

                    if($end < $totalPages)
                    {                        
                        self::a(null, "...", $class["class"]);
                        self::a($totalPages, $totalPages, $class["class"]);
                    }
                }
            }

            if(self::$visible["pagePreviousAndNext"] === true)
                self::a(((self::$page == $totalPages) ? $totalPages : self::$page + 1), self::$previousAndNextLabel[1] , $class["class"]);
        
            if(self::$visible["pageFirstAndLast"] === true)
                self::a($totalPages, self::$firstAndLastLabel[1] , $class["class"]);
            
            if(self::$asUl === true)
            {
                self::li();
                return self::ul();
            }else
            {
                return self::div();
            }
        
        endif;             
    }

    /**
     * @example array('<a href="http://yourdomian.com?page=1" class="$class">', '<a href="http://yourdomian.com?page=2" class="$class">')
     * @param String $page - integer number of the pagination, retrieved via http request
     * @param String $content - displayed text in the pagination link
     * @param array $classes - pagination link class
     * @return array - returns an array with all pagination links
     */
    private static function a($page, $content, array $classes = [])
    {
        $url        = !empty(self::$url) ? trim(self::$url, "/") ."/" : "?".self::$pageHttpLabel."=";

        if(!is_null($page))
        {
            self::$anchors[] = sprintf('<a href="' . $url. '%d" class="%s">%s</a>', $page, implode(" ", $classes), $content);
        }else
        {
            self::$anchors[] = sprintf('<a href="javascript:void(0)" class="%s">%s</a>',  implode(" ", $classes), $content);
        }
        
        return self::$anchors;
    }

    /**
     * @example array('<li class="$class"><a href="http://yourdomian.com?page=1" class="$class"></a></li>', '<li class="$class"><a href="http://yourdomian.com?page=2"></a></li>')
     * @return array returns an array with all links wrapped in list items
     */
    private static function li()
    {
        $class = self::getClassValue("li", "class");
        
        foreach(self::$anchors as $anchor)
        {
            self::$lists[] = sprintf("\n<li class='%s'>%s</li>", implode(" ", $class["class"]), $anchor);
        }    
        
        return self::$lists;
    }

    /**
     * @example <ul class="$class"><li><a href="http://yourdomian.com?page=1" class="$class"></a></li></ul>
     * @return string returns all the list item wrapped in the ul
     */
    private static function ul()
    {
        $class = self::getClassValue("ul", "class");
        
        $lists = implode("", self::$lists);
		
        return sprintf("<ul class='%s'>%s\n</ul>", implode(" ", $class["class"]), $lists);
    }

    /**
     * @example <div class="$class"><a href="http://yourdomian.com?page=1" class="$class"></a></div>
     * This functions is triggered when the self::$asUl is set to false
     * @return string returns list of links wrapped in the div, without the list item
     */
    private static function div()
    {
        $class = self::getClassValue("div", "class");
        
        $anchors = implode("\n", self::$anchors);  
		
        return sprintf('<div class="%s">%s</div>', implode(" ", $class["class"]), $anchors);
    }

    /**
     * Checks and returns the class if it exists
     * @param $key
     * @param $key1
     * @return array - returns the class if exists or empty if not
     */
    private static function getClassValue(String $key, String $key1)
   {
        return array_key_exists($key, self::$classes) ? 
        (
            array_key_exists($key1, self::$classes[$key])
            ? self::$classes[$key] : []
        ) : [];
   }
}