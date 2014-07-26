<?php
/*
Plugin Name: Whale-Kit
Plugin URI: http://www.wp.od.ua/en/?p=33
Description: Two advanced widgets and two shortcodes. 1) WK_trem working with categories, post_tag or any taxonomies. Settings from function get_terms(). 2) WK_posts works with posts, pages and any other type of records. Settings from class WP_Query. Advanced Settings to display data and rules for constructing micro-patterns, see <a href="http://www.wp.od.ua/en/?p=33">page plugin</a>. Russian page plugin <a href="http://wp.od.ua/?p=1261">Whale-Kit</a>
Author: Yuriy Stepanov (stur)
Version: 1.0.1
Author URI: http://wp.od.ua/
*/
define("WHALE_KIT_ENABLE", 1);
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'wpautop' , 12);

require_once ( dirname(__FILE__).'/wk-terms.php' );
require_once ( dirname(__FILE__).'/wk-posts.php' );

class WK_Terms_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'WK_Terms_Widget', // Base ID
			__('WK Terms Widget', 'whalekit'), // Name
			array( 'description' => __( 'WK Terms Widget', 'whalekit' ), ) // Args
		);
	}

    function widget($all_wd_options, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $wk_terms = new WK_terms;
        $out = $wk_terms->w($instance['args']);
        extract($all_wd_options);
        if ($out) {
            $title = apply_filters('widget_title', $instance['title']);
            if ($title)
                $title = "$before_title$title$after_title";
            $out = "\n$before_widget\n$title\n$out\n$after_widget\n";
        }
        echo $out;
    }

    function update($new_instance, $old_instance) {
        $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['args']  = $new_instance['args'];
		return $instance;
    }

    function form($instance) {
        extract($instance);
        if(!$title) $title =  __( 'New title');
        if(!$args) $args = 'taxonomy=category&show_count=1&collapse=1';

?>
       <div style="width: 640px; margin-left:-300px; margin-top: 60px; z-index:10; position: absolute;  padding:2em;   background-color: #FFFFFF; border: solid 1px #6A6A6A">
        <p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'args' ); ?>"><?php _e( 'Function arguments:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'args' ); ?>" name="<?php echo $this->get_field_name( 'args' ); ?>" type="text" value="<?php echo esc_attr( $args ); ?>">
		</p>
        <br />
        Help function arguments:  basic parameters <a href="http://codex.wordpress.org/Function_Reference/get_terms">get_terms()</a>,  additional settings <a href="http://www.wp.od.ua/en/?p=76">WK_terms</a><br>
        Russian help:  <a href="http://wp.od.ua/?p=1271">WK_terms</a>
      </div>
<?php
      }
}

add_action('widgets_init', create_function('', 'return register_widget("WK_Terms_Widget");'));



// регистрация шоткодов, registration shortcode
function wk_terms($atts) {
    $wk_terms = new WK_terms;
    return $wk_terms->w($atts);
}
add_shortcode('wk_terms', 'wk_terms');

function wk_posts($atts) {
    $wk_posts = new WK_posts;
    return $wk_posts->w($atts);
}
add_shortcode('wk_posts', 'wk_posts');



class WK_Post_Widget extends WP_Widget{

	function __construct() {
		parent::__construct(
			'WK_Post_Widget', // Base ID
			__('WK Post Widget', 'whalekit'), // Name
			array( 'description' => __( 'WK Post Widget', 'whalekit' ), ) // Args
		);
	}

    function widget($all_wd_options, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $wk_post = new WK_posts;
        $out = $wk_post->w($instance['args']);

        extract($all_wd_options);
        if ($out) {
            $title = apply_filters('widget_title', $instance['title']);
            if ($title)
                $title = "$before_title$title$after_title";
            $out = "\n$before_widget\n$title\n$out\n$after_widget\n";
        }
        echo $out;
    }

    function update($new_instance, $old_instance) {
        $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['args']  = $new_instance['args'];
		return $instance;
    }

    function form($instance) {
        extract($instance);
        if(!$title) $title =  __( 'New title');
        if(!$args) $args = 'post_type=page&show_count=1&posts_per_page=10';

?>
       <div style="width: 640px; margin-left:-300px; margin-top: 60px; z-index:10; position: absolute;  padding:2em;   background-color: #FFFFFF; border: solid 1px #6A6A6A">
        <p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'args' ); ?>"><?php _e( 'Function arguments:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'args' ); ?>" name="<?php echo $this->get_field_name( 'args' ); ?>" type="text" value="<?php echo esc_attr( $args ); ?>">
		</p>
        Help function arguments:  basic parameters <a href="http://codex.wordpress.org/Class_Reference/WP_Query">WP_Query</a>,  additional settings <a href="http://www.wp.od.ua/en/?p=80">WK_posts</a><br>
        Russian help:  <a href="http://wp.od.ua/?p=1272">WK_posts</a>
      </div>
<?php
      }
}
add_action('widgets_init', create_function('', 'return register_widget("WK_Post_Widget");'));






class WK_tree {

/*
если нам передан параметр где первым словом идет array значит это массив
запускаем eval() и создаем массив из строки
if we passed as parameter where the first word is an array of array means
run eval () and create an array of strings
*/
function eval_array(& $atts){
    foreach ($atts as $key => $value) {
        if( strpos ($value, 'array') === 0  ){
            eval('$atts[$key] = '.$value.';');
        }
    }
    return $atts;
}

/*
$arr - ассациотивный массив
$str - строка шаблон
функция микро - шаблонизатор
принимает acсациотивный массив к ключам добавляем $
затем меняем все вхождения в строке на типа ==$varname== значения массива

function micro-templating
takes acsatsiotivny array keys to add $
then change all occurrences in a string type == $varname == array values
*/
    function str_replace_var($arr, $str){
        if(!$str) return '';
        uksort($arr, array($this, "cmp") );
        $keys = array_keys($arr);
        $values = array_values($arr);
        foreach ($keys as $i => $value) {
            $keys[$i] = '$'.$value;
        }
        $keys[] = '{'; $values[] = '<';
        $keys[] = '}'; $values[] = '>';
        $keys[] = '  '; $values[] = ' ';
        $keys[] = '\n'; $values[] = "\r\n";
        $keys[] = ' >'; $values[] = '>';
        $keys[] = ' style=""'; $values[] = '';
        $keys[] = ' title=""'; $values[] = '';
        $st = str_replace($keys, $values, $str);
        return $st;
    }

    function cmp($a, $b)
    {
        if (strlen($a) == strlen($b))
            return 1;
        if (strlen($a) > strlen($b))
            return 0;
        return 1;
    }
/*
печатает уровень элементов
prints the element level
*/
   function level($elements, $depth, & $r) {
// проверка уровня  depth
    if ( !sizeof($elements) || ( isset($r['depth']) && ( (int)$r['depth'] < ($depth+1) ) ) )
      return '';

// сначала запускаем печать елементов уровня
//first print run bits and pieces of this level
    $num = 1;
    foreach ($elements as $id => $term) {
      $arr['elements'] .= $this->element($term, $depth, $r, $num);
      $num++;
    }


    if(empty($r['lv_tmpl']))
        return $arr['elements'];
// какой html тег будет содержать эелементы
//what html tag will contain eelementy
    $arr['lv_tag'] = $r['lv_tag'];
    $arr['taxonomy'] = $r['taxonomy'];
    $arr['css_prefix'] = $r['css_prefix'];
    $arr['el_count'] = sizeof($elements);
    $arr['depth'] = $r['depth'];
// отступ для красоты в коде  indent
    $arr['ind'] = str_repeat("\t", $depth);
// готовим css класс
//preparing css class
    if ($depth > 0)
      $arr['class'] = "childs depth-$depth";
    else
      $arr['class'] = "root depth-$depth";
// формируем уровень
//form a level
    $out = $this->str_replace_var($arr, $r['lv_tmpl']);
    return $out;
  }


/*
из обычного массива делает древовидную структуру
'parent' => 'parent', 'id' => 'term_id'   у таксономий
'parent' => 'post_parent', 'id' => 'ID'   у постов
of ordinary array makes the tree structure
*/

     function tree($elements, & $r) {
        $el = $elements[0];// берем первый элемент - take the first element

/*
    определяем с чем мы имеем дело токсномии или записи
    устанавливаем имена полей id и parent
define what we are dealing with taxonomy or recording
     set the field names and id parent
*/
        if($el->term_id AND  isset($el->parent) ){
            $name_id = 'term_id';
            $name_parent = 'parent';
        }
        elseif ($el->ID AND  isset($el->post_parent) ){
            $name_id = 'ID';
            $name_parent = 'post_parent';
        }

//переделываем исходный массив id елемента ставим как первичный ключ массива
//remake the original array id elementa set as the primary key of the array
        foreach ($elements as $key => $element) {
          $arrtmp[$element->$name_id] = $element;
        }

        $elements = array();
        foreach ($arrtmp as $id => $element) {
          if ($arrtmp[$element->$name_parent]) {
            if(!$arrtmp[$element->$name_parent]->childs)
                $arrtmp[$element->$name_parent]->childs = array();
             $arrtmp[$element->$name_parent]->childs[$id] = & $arrtmp[$id];

          }
/*
запоминаем текущий элемент, remember the current element, текущих элементов может быть много
если запись принадлежит нескольким категориям то надо отметить все категории как текущие
remember the current item, remember the current element, current elements can be many
if the record belongs to more than one category it should be noted that all categories as current
*/
        if ($element->current)
            $currents[] = $id;
        }
//от текущуго элемента вверх по родительской цепочке отмечаем родителей
//from the current element up the parent chain celebrate parents
        if (sizeof($currents)){
            foreach ($currents as $key => $current) {
                 $this->set_current_parent($arrtmp, $current, $name_parent);
            }
        }




// удаляем лишние элементы и делаем коллапс если он включен
//remove unnecessary elements and do collapse if it is included
        foreach ($arrtmp as $id => $element) {
// отмечаем элементы у которых есть потомки
//note items that have descendants
          if($element->childs)
             $element->has_children = 1;
// коллапс обрезаем не активные ветки
//collapse cut off inactive branches
          if($r['collapse'] AND $element->childs
                AND !($element->current OR $element->current_parent OR $element->ancestor) ){
              unset($element->childs);
          }

          if (!$arrtmp[$element->$name_parent])
            $elements[$id] = $element;
        }
        return $elements;
    }

/*
вспомогательная функция- рекурсивно отмечает родителей начиная от активного узла
вверх по иерахии активному родителю устанавливается свойство current_parent = 1

recursively parents notes from the active node
up ierahii active parent property is set current_parent = 1
*/
    function set_current_parent(& $elements, $current, $name_parent) {
        if (isset ($elements[$current]->$name_parent)) {
          $paren_id = $elements[$current]->$name_parent;
          if (isset ($elements[$paren_id])) {
              $elements[$paren_id]->ancestor = 1;
              if($elements[$current]->current)
                    $elements[$paren_id]->current_parent = 1;
            $this->set_current_parent($elements, $paren_id, $name_parent);
          }
        }
    }
/*
обрезает текст до заданной величины не разрывая слов
obscures the text to the desired value without breaking words
*/
    function sub_text($st, $len){
       if(function_exists('mb_strlen'))
            $utf8_func = 'mb_strlen';
       else
            $utf8_func = 'strlen';

       if($utf8_func($st)<=$len) return trim($st);
       $st = strip_tags($st);
       $st = str_replace('<!--more-->','',$st);
       $arr = preg_split('~([\s\,\.\?\!\;\-]+)~u', $st,-1,PREG_SPLIT_DELIM_CAPTURE);
       $st = '';
       foreach ($arr as $key=>$value) {
          if( $utf8_func($st.$value) < $len )
            $st .= $value;
          else
             return trim( $st );
       }
       return trim($st);
    }
}