<?php
/*
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

description of the parameters is available at http://www.wp.od.ua/en/?p=80
*/
class WK_posts extends WK_tree{
public function w($args=null){
    $defaults = array(
    'authors' => '',  'post_type' => 'post', 'posts_per_page'=>10,
    'hierarchical' => 0,
    'smallest' => 8, 'largest' => 22, 'unit' => 'pt',
    'date_format' => get_option('date_format'),
    'lv_tag' => 'ul', 'el_tag' => 'li', 'count_tag' => 'sup',  'indent'=>"\t",
    'lv_tmpl'=>'\n$ind<$lv_tag  class="$class">\n$elements\n$ind</$lv_tag>',
    'с_tmpl'=>'<$count_tag>$count</$count_tag>',
    'el_tmpl'=>'<$el_tag class="$class" title="$hint"><a href="$href" style="$style">$title</a>$с_tmpl$cnt_tmpl$childs</$el_tag>',
    );

    $r = wp_parse_args($args, $defaults);
    $this->eval_array($r);

    if($r['collapse'])
        $r['hierarchical'] = 1;
    if(!$r['css_prefix'])
        $r['css_prefix'] = $r['post_type'];

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
*/  global $wp_query;
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


/*
если установлен size_of_count расчитаем размер шрифта в зависимости от кол-ва
записей которое содержит каждая таксономия
размер шрифта запоминаем в свойстве ->font_size

if installed size_of_count will calculate the font size depending on the number of
record that contains each taxonomy
font size is stored in the property -> font_size
*/
    if($r['size_of_count']) {
        $min_count =  min($counts);
        $spread_count = max($counts) - $min_count;
        $spread_font =$r['largest'] - $r['smallest'];
        if($spread_count>0){
            $k = $spread_font/$spread_count;
            foreach ($posts as $key => $value) {
                $posts[$key]->font_size = round($r['smallest'] + (($value->comment_count -$min_count)*$k), 2);
            }
        }

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

    [ID] => 147
    [post_author] => 1
    [post_date] => 2014-04-13 02:29:53
    [post_date_gmt] => 2014-04-12 22:29:53
    [post_content] => жил я как-то в общаге и среди всех остальных соседей были (м)ама
    [post_title] => жил я как-то в общаге и среди всех остальных соседей были
    [post_excerpt] =>
    [post_status] => publish
    [comment_status] => open
    [ping_status] => open
    [post_password] =>
    [post_name] => %d0%b6%d0%b8%d0%bb-%d1%8f-%d0%ba%d0%b0%d0%ba-%d1%82%d0%be-%d0%b2-%d0%be%d0%b1%d1%89%d0%b0%d0%b3%d0%b5-%d0%b8-%d1%81%d1%80%d0%b5%d0%b4%d0%b8-%d0%b2%d1%81%d0%b5%d1%85-%d0%be%d1%81%d1%82%d0%b0%d0%bb
    [to_ping] =>
    [pinged] =>
    [post_modified] => 2014-04-13 02:29:53
    [post_modified_gmt] => 2014-04-12 22:29:53
    [post_content_filtered] =>
    [post_parent] => 127
    [guid] => http://wp38/?page_id=147
    [menu_order] => 0
    [post_type] => page
    [post_mime_type] =>
    [comment_count] => 0
    [filter] => raw

*/