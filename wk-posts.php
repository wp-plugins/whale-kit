<?php
/*
http://www.wp.od.ua/en/?page_id=366

используем микро-шаблоны
для вывода элементов       &apos; &quot;      &lt;    &gt;

collapse - скрывать неактивные ветки дерева, позволяет существенно сократить список
show_count - выводить кол-во комментариев
size_of_count -  рассчитать размер шрифта в зав. от кол-ва комментариев
smallest,largest - установка размеров шрифта от маленького до большого,
каждому элементу в ссылку будет установлен шрифт  <a href="" style='font-size:8pt'>
в зависимости от кол-ва коментов которое содержит данная запись\страница
unit - указывает единицу измерения шрифта  п.у. pt
css_prefix - используется при формировании css классов элемента
truncate_title - обрезать заголовок до размера
truncate_content - обрезать основной текст
show_author - вычислить автора, в шаблонах станет доступна переменная $author
show_thumbnail - показывать миниатюру записи, в шаблонах станет доступна переменая картинка $img
none_thumbnai - заглушка, если запись не имеет миниатюру,  тут указать id загрузки

***МИКРО ШАБЛОНЫ***
hint_none - - текст титла страницы у которой нет коментов
hint_single - текст титла страницы у которой 1 комент
hint_fiw   - текст титла страницы у которой 2-4 комента
hint  -  текст титла страницы у которой 5 и более коментов
если первые 2 не заданы используется hint   &laquo;&raquo;

lv_tmpl - микро шаблон уровня
el_tmpl - микро шаблон элемента
с_tmpl - микро шаблон кол-ва коментариев
cnt_tmpl - шаблон контента для post_content
a_tmpl  - шаблон вывода автора пример:  'автор: <a href="$a_url">$author</a>';
- по умолчанию шаблон не задан

пример шоткода: выводит посты таблицей
[wk_posts post_type="post" lv_tmpl='<h2>Таблица</h2><table>$elements</table> пример таблицы' show_author=1 el_tmpl='<tr><td>$id<td><a href="$href"> $title</a><td>$a_tmpl<td>$date</tr>' date_format='d-m-Y H ч.'/]

*/
class WK_posts extends WK_tree{

public function w($args=null){
/*   установка опций  installation options */
    $r = $this->set_args($args);

/* получение данных   data acquisition  */
    $query = new WP_Query($r);
    if(!sizeof($query->posts))
        return '';
    $posts  = $query->posts;

/*
установка текущего элемента
также есть возможность устанавливать ткущий элемент через массив аргументов
на этом этапе нам просто нужно отметить элемент полем 'current'
Set the current element
also have the opportunity to establish weaving element in an array of arguments
at this stage we just need to mark an item field 'current'
*/
   $this->set_current($posts, $r);

/*
если установлен size_of_count расчитаем размер шрифта в зависимости от кол-ва
записей которое содержит каждая таксономия
размер шрифта запоминаем в свойстве ->font_size

if installed size_of_count will calculate the font size depending on the number of
record that contains each taxonomy
font size is stored in the property -> font_size
*/
    if($r['size_of_count']) {
        $this->cal_font_size($posts, $r);

    }

/*

 */
    if ($r['hierarchical']){
        $posts = $this->tree( $posts , $r);
        //echo '<pre>'; print_r($posts); echo '</pre>';
        $out = $this->level( $posts, 0, $r);
    } else {
       $out = $this->level( $posts, 0, $r);
    }

    return  $out;
}


function set_args($args){
    $defaults = array(
    'authors' => '',  'post_type' => 'post', 'posts_per_page'=>-1,
    'hierarchical' => 0,
    'smallest' => 8, 'largest' => 22, 'unit' => 'pt',
    'date_format' => get_option('date_format'),
    'lv_tag' => 'ul', 'el_tag' => 'li', 'count_tag' => 'sup',  'indent'=>"\t",
    'lv_tmpl'=>'\n$ind<$lv_tag  class="$class">\n$elements\n$ind</$lv_tag>',
    'с_tmpl'=>'<$count_tag>$count</$count_tag>',
    'el_tmpl'=>'<$el_tag class="$class" title="$hint">$img<a href="$href" style="$style">$title</a>$с_tmpl$cnt_tmpl$childs</$el_tag>',
    );


    $r = wp_parse_args($args, $defaults);

    if(is_singular() ){
        if( $r['post_parent'] == '$this' )
            $r['post_parent'] = get_the_ID();

        if($r['post_parent__in'])
            $r['post_parent__in'] = str_replace('$this', get_the_ID(), $r['post_parent__in']);

        if($r['post__in'])
            $r['post__in'] = str_replace('$this', get_the_ID(), $r['post__in']);

        if($r['post__not_in'])
            $r['post__not_in'] = str_replace('$this', get_the_ID(), $r['post__not_in']);
    }

    if( $r['show_thumbnail'] ){
        if( $r['show_thumbnail'] == 'thumbnail' OR $r['show_thumbnail'] == 'medium'
            OR $r['show_thumbnail'] == 'large' OR $r['show_thumbnail'] == 'full' )
            $r['img_size'] = $r['show_thumbnail'];
        elseif( preg_match('~^(\d+)[x,X](\d+)~',$r['show_thumbnail'], $match) ) {
            $r['img_size'] = array($match[1],$match[2]);
        }
    }
    $this->eval_array($r);
    if($r['collapse'])
        $r['hierarchical'] = 1;
    if(!$r['css_prefix'])
        $r['css_prefix'] = $r['post_type'];
    return $r;
}

function set_current(& $posts, & $r){
    global $wp_query;
    if (is_single() || is_page() || is_attachment() || $wp_query->is_posts_page )
        $r['current']  = $wp_query->get_queried_object_id();

    foreach ($posts as $key => $value) {
      if ($value->ID == $r['current'])
        $posts[$key]->current = 1;
        if($value->comment_count)
            $counts[] =  $value->comment_count;
        else
           $counts[] =  0;
    }
    $r['counts'] = $counts;
}


function cal_font_size(& $posts, & $r){
        $min_count =  min($r['counts']);
        $spread_count = max($r['counts']) - $min_count;
        $spread_font =$r['largest'] - $r['smallest'];
        if($spread_count>0){
            $k = $spread_font/$spread_count;
            foreach ($posts as $key => $value) {
                $posts[$key]->font_size = round($r['smallest'] + (($value->comment_count -$min_count)*$k), 2);
            }
        }
}

function element($p, $depth, & $r, $num){
    $arr['id'] = $id = $p->ID;
    $arr['css_prefix'] = $pr = $r['css_prefix'];
    $arr['count_tag'] = $r['count_tag'];
    $arr['font_size'] = $p->font_size;
    $arr['post_type'] = $p->post_type;
    $arr['content'] = strip_tags( $p->post_content );
    $arr['excerpt'] = strip_tags( $p->post_excerpt );
    $arr['el_tag'] = $r['el_tag'];
    $arr['title'] = $p->post_title;
    $arr['count'] = $p->comment_count;
    $arr['date'] = date( $r['date_format'], strtotime($p->post_date)  );
    $arr['href'] = get_permalink($p->ID);
    $arr['unit'] = $unit = $r['unit'];
    $arr['name'] = $p->post_name;
    $arr['ind'] = str_repeat("\t", $depth + 1);
    // порядковый номер элемента по списку, serial number of the item in the list
    $arr['num'] = $num;



// css классы для элемента  css classes for the element
    if ($p->current)
      $current = " current_{$pr}";
    elseif ($p->current_parent)
      $current = " current_{$pr}_parent";
    if ($p->ancestor)
        $ancestor =  " current_{$pr}_ancestor";
    if($p->has_children)
       $has_childs = " {$pr}_has_children";
    $arr['class'] = "{$pr}_item {$pr}_$id $ancestor $has_childs $current";

// показывать автора или нет, show website or not
   if($r['a_tmpl'] || $r['show_author']){
        $userdata = get_userdata($p->post_author);
        $arr['author'] = $userdata->display_name;
        $arr['a_url'] = get_author_posts_url($p->post_author);
        $arr['a_tmpl'] = $this->str_replace_var( $arr,  $r['a_tmpl'] );
    }

    if( $r['show_thumbnail'] ){
        if(has_post_thumbnail( $id )){
            if($r['img_size'])
                $arr['img'] = get_the_post_thumbnail( $id,  $r['img_size'] );
            else
                 $arr['img'] = get_the_post_thumbnail( $id );
        }elseif($r['none_thumbnai']){
            if($r['img_size'])
                $arr['img'] = wp_get_attachment_image( $r['none_thumbnai'],  $r['img_size'] );
            else
                 $arr['img'] = wp_get_attachment_image( $r['none_thumbnai'] );
        }
    }else{
            $arr['img'] = '';
    }

//  hint
    if( $r['hint_none'] && $p->comment_count == 0 )
        $hint = $r['hint_none'];
    elseif( $r['hint_single'] &&  $p->comment_count == 1 )
        $hint = $r['hint_single'];
    elseif ($r['hint_few'] &&  ( ($p->comment_count > 1) &&  ($p->comment_count < 5) )  )
        $hint = $r['hint_few'];
    else
        $hint = $r['hint'];
    $arr['hint'] = esc_attr( $this->str_replace_var($arr, $hint) ) ;

// обрезание заголовка и контента, truncate header text and content
    if($r['truncate_title'])
        $arr['title'] = $this->sub_text($p->post_title, $r['truncate_title']);
    if($r['truncate_content']){
        $arr['content'] = $this->sub_text($p->post_content, $r['truncate_content']);
        if(!$p->post_excerpt)
            $arr['excerpt'] =str_replace(array("\r\n", "\n", '<br />'), ' ', $arr['content']);
    }

// content
    if($r['cnt_tmpl'])
       $arr['cnt_tmpl'] = $this->str_replace_var( $arr,  $r['cnt_tmpl'] );
    else
       $arr['cnt_tmpl'] = '';
//size_of_count
//установка размера шрифта для каждой ссылки, set the font size for each link
    if($p->font_size)
        $arr['style']  = "font-size:$p->font_size$unit;";
    else
        $arr['style'] = '';

// кол-во коментов в записи с_tmpl, count of comments in posts\page
    if ($p->comment_count AND $r['show_count'])
       $arr['с_tmpl'] =  $this->str_replace_var($arr, $r['с_tmpl']);
    else
        $arr['с_tmpl'] =  '';



    if ($p->childs)
        $arr['childs'] = $this->level($p->childs, $depth + 1, $r);
    else
        $arr['childs'] = '';


    $out = $this->str_replace_var($arr, $r['el_tmpl']);
    return $out;
}

} // end class WK_posts



/*
отличается от WK_постс источником данных - данные получаем с помощью функции  get_pages
differs from the WK post to a data source - data is obtained using the  get_pages()
*/
class WK_pages extends WK_posts{
public function w($args=null){
/*   установка опций  installation options */
    $r = $this->set_args($args);

/* получение данных   data acquisition  */
    $posts = get_pages($r);
    if(!sizeof($posts))
        return '';

/*
установка текущего элемента
также есть возможность устанавливать ткущий элемент через массив аргументов
на этом этапе нам просто нужно отметить элемент полем 'current'
Set the current element
also have the opportunity to establish weaving element in an array of arguments
at this stage we just need to mark an item field 'current'
*/
   $this->set_current($posts, $r);

/*
если установлен size_of_count расчитаем размер шрифта в зависимости от кол-ва
записей которое содержит каждая таксономия
размер шрифта запоминаем в свойстве ->font_size

if installed size_of_count will calculate the font size depending on the number of
record that contains each taxonomy
font size is stored in the property -> font_size
*/
    if($r['size_of_count']) {
        $this->cal_font_size($posts, $r);

    }

/*

 */
    if ($r['hierarchical']){
        $posts = $this->tree( $posts , $r);
        //echo '<pre>'; print_r($posts); echo '</pre>';
        $out = $this->level( $posts, 0, $r);
    } else {
       $out = $this->level( $posts, 0, $r);
    }

    return  $out;
}

function set_args($args){
    $defaults = array(
    'authors' => '',  'post_type' => 'page',
    'hierarchical' => 1,
    'smallest' => 8, 'largest' => 22, 'unit' => 'pt',
    'date_format' => get_option('date_format'),
    'lv_tag' => 'ul', 'el_tag' => 'li', 'count_tag' => 'sup',  'indent'=>"\t",
    'lv_tmpl'=>'\n$ind<$lv_tag  class="$class">\n$elements\n$ind</$lv_tag>',
    'с_tmpl'=>'<$count_tag>$count</$count_tag>',
    'el_tmpl'=>'<$el_tag class="$class" title="$hint">$img<a href="$href" style="$style">$title</a>$с_tmpl$cnt_tmpl$childs</$el_tag>',
    );


    $r = wp_parse_args($args, $defaults);

    if(is_singular() ){
        if( $r['post_parent'] == '$this' )
            $r['post_parent'] = get_the_ID();

        if($r['post_parent__in'])
            $r['post_parent__in'] = str_replace('$this', get_the_ID(), $r['post_parent__in']);

        if($r['post__in'])
            $r['post__in'] = str_replace('$this', get_the_ID(), $r['post__in']);

        if($r['post__not_in'])
            $r['post__not_in'] = str_replace('$this', get_the_ID(), $r['post__not_in']);
    }

    if( $r['show_thumbnail'] ){
        if( $r['show_thumbnail'] == 'thumbnail' OR $r['show_thumbnail'] == 'medium'
            OR $r['show_thumbnail'] == 'large' OR $r['show_thumbnail'] == 'full' )
            $r['img_size'] = $r['show_thumbnail'];
        elseif( preg_match('~^(\d+)[x,X](\d+)~',$r['show_thumbnail'], $match) ) {
            $r['img_size'] = array($match[1],$match[2]);
        }
    }
    $this->eval_array($r);
    if($r['collapse'])
        $r['hierarchical'] = 1;
    if(!$r['css_prefix'])
        $r['css_prefix'] = $r['post_type'];
    return $r;
}

}