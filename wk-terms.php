<?
class WK_terms extends WK_tree{
/*
description of the parameters is available at http://www.wp.od.ua/en/?page_id=333
основной метод вывода    &apos; &quot;      &lt;    &gt;  #171792 &amp;
подготовка установка значений по умолчанию
collapse - скрывать неактивные ветки дерева, позволяет существенно сократить список
current - можно указать текущую таксономию
depth - максимальный уровень
show_count - выводить кол-во записей в категории(таксономии)
size_of_count -  рассчитать размер шрифта в зав. от кол-ва записей в категории(таксономии)
smallest,largest - установка размеров шрифта от маленького до большого,
каждому элементу в ссылку будет установлен шрифт  <a href="" style='font-size:8pt'>
в зависимости от кол-ва записей которое содержит данная таксономия
unit - указывает еденици измерения шрифта  п.у. pt

lv_tag -html тег для инкапсуляции уровня п.у. ul
el_tag - html тег для элемента списка п.у. li
count_tag - html тег для отражения числа записей в таксономии п.у. <sup>67</sup>
use_desc_for_hint - использовать описание категории для всплывающей подсказки в hint=""
***МИКРО ШАБЛОНЫ***
hint_none -   нет записей
hint_single - текст титла для метки в которой одна запись: 1 запись
hint_fiw   - текст для нескольких записей :  2 записи, 3 записи
hint  - текст для метки у которой много записей: 8 записей, 1200 записей
он же используется по умолчанию если не назначены hint_fiw и hint_single
пример:
    hint_none = 'нет записей в «$name» id:$id'
    hint_single = '1 запись в категории «$name» id:$id'
    hint_few='$count записи в категории «$name» id:$id'
    hint='$count записей в категории «$name» id:$id'
доступные переменные: $count, $name, $id

lv_tmpl - микро шаблон уровня
el_tmpl - микро шаблон элемента
t_count - микро шаблон кол-ва записей

пример:
lv_tmpl='\n$ind<$lv_tag  class="$class myclass">\n$elements\n$ind</$lv_tag>'
*внутри шаблона использовать только двойные кавычки
переменные для lv_tmpl=>(elements, lv_tag, taxonomy, css_prefix, el_count, ind, class )
для  t_count =>( count_tag id  count )
для el_tmpl=> (ind, href, el_tag, count_tag, css_prefix, id, unit, name, count,
font_size, class, hint, style, t_count, childs )

пример вывод категорий ввиде таблицы:
[wk_terms hierarchical=0 taxonomy=category  lv_tmpl='<table>$elements\n</table>' el_tmpl='<tr><td>id:$id</td><td>$name</td><td>count:$count</td></tr>' /]

taxonomy=category&show_count=1&hierarchical=1&size_of_count=1&smallest=9&largest=20&hint=в рубрике $name - $count записей&hint_single=одна запись в рубрике $name&hint_fiw=$count записи в рубрик $name



*/
  public function w($args = null) {

    $defaults = array('taxonomy' => 'category', 'css_prefix' => 'cat', 'hierarchical' =>1,
    'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false,
    'hierarchical' => true, 'smallest' => 8, 'largest' => 22, 'unit' => 'pt',
    'lv_tag' => 'ul', 'el_tag' => 'li', 'count_tag' => 'sup',  'indent'=>"\t",
    'hint' => '$name',
    'lv_tmpl'=>'\n$ind<$lv_tag  class="$class">\n$elements\n$ind</$lv_tag>',
    'с_tmpl'=>'<$count_tag>$count</$count_tag>',
    'el_tmpl'=>'\n$ind<$el_tag class="$class" title="$hint"><a href="$href" style="$style">$name</a>$с_tmpl$childs</$el_tag>',


    );
    $r = wp_parse_args($args, $defaults);
    $this->eval_array($r);

    if (!isset ($r['pad_counts']) && $r['show_count'] && $r['hierarchical'])
      $r['pad_counts'] = true;

/*
получаем набор данных - выводим ошибки и если данных нет, просто  выходим
obtain a set of data - Displays error and if not available, simply leave
*/
    $terms = get_terms($r['taxonomy'], $r);

    if (is_wp_error($terms))
      return $terms->get_error_message();
    if (!sizeof($terms))
      return false;
/*
установка текущего элемента
также есть возможность устанавливать ткущий элемент через массив аргументов
на этом этапе нам просто нужно отметить элемент свойством 'current'

Set the current element
also have the opportunity to establish weaving element in an array of arguments
at this stage we just need to mark an item property 'current'
*/
    if (empty ($r['current']) && (is_category() || is_tax() || is_tag())) {
      $current_term_object = get_queried_object();
      if ($current_term_object && $r['taxonomy'] === $current_term_object->taxonomy)
        $r['current'] = get_queried_object_id();
    }
    elseif( is_single() ){
        $single  = get_queried_object_id();
        $single_terms = wp_get_post_terms( $single, $r['taxonomy'] );
        if(sizeof($single_terms) ){
            foreach ($single_terms as $key => $value) {
                $r['current'][] = $value->term_id;
            }
        }
    }

// отмечаем текущие элементы и считаем кол-во записей   
    foreach ($terms as $key => $value) {
       if( is_array($r['current']) ){
           if( in_array( $value->term_id, $r['current'] ) )
             $terms[$key]->current = 1;
       }
       else {
           if ($value->term_id == $r['current'])
             $terms[$key]->current = 1;
       }

        if($value->count)
            $counts[] =  $value->count;
        else
           $counts[] =  0;
    }


/*если установлен size_of_count расчитываем размер шрифта в зависимости от кол-ва
записей которое содержит каждая таксономия
размер шрифта запоминаем в свойстве ->font_size

if installed size_of_count are counting font size depending on the number of
record that contains each taxonomy
font size is stored in the property -> font_size
*/
    if($r['size_of_count']) {
        $min_count =  min($counts);
        $spread_count = max($counts) - $min_count;
        $spread_font =$r['largest'] - $r['smallest'];
        if($spread_count>0){
            $k = $spread_font/$spread_count;
            foreach ($terms as $key => $value) {
                $terms[$key]->font_size = round($r['smallest'] + (($value->count -$min_count)*$k), 2);
            }
        }
    }

    if ($r['hierarchical']) {
      $terms = $this->tree($terms, $r);
      $out = $this->level($terms, 0, $r);
    }
    else
      $out = $this->level($terms, 0, $r);

    return $out;
  }

 /*
печатает элемент списка
если у елемента обнаружен $term->childs - значит у него есть дочернии елементы
вызывается родительский метод  level()

prints a list item
if detected elementa $ term-> childs - means he has a child Components
method is called the parent level ()
*/
   function element($term, $depth, & $r, $num) {
    $arr['count_tag'] = $r['count_tag'];
    $arr['css_prefix'] = $p = $r['css_prefix'];
    $arr['font_size'] = $term->font_size;
    $arr['el_tag'] = $r['el_tag'];
    $arr['count'] = $term->count;
    $arr['depth']  = $depth;
    $arr['unit'] = $unit = $r['unit'];
    $arr['href'] = get_term_link($term, $r['taxonomy']);
    $arr['name'] = $term->name;
    $arr['ind'] = str_repeat("\t", $depth + 1);
    // порядковый номер элемента по списку, serial number of the item in the list
    $arr['num'] = $num;
    $arr['id'] = $id = $term->term_id;

// собираем css классы, collect css classes
    if ($term->current)
      $current = " current_$p";
    elseif ($term->current_parent)
      $current = " current_{$p}_parent";
    if ($term->ancestor)
        $ancestor =  " current_{$p}_ancestor";
    if($term->has_children)
       $has_childs = " {$p}_has_children";
    $arr['class'] = "{$p}_item {$p}_$id $ancestor $has_childs $current";

// всплывающая подсказка, tooltip
    if ($r['use_desc_for_hint'] == 0 || empty ($term->description)){
        if( $r['hint_none'] &&  $term->count == 0 )
            $hint = $r['hint_none'];
        elseif( $r['hint_single'] &&  $term->count == 1 )
            $hint = $r['hint_single'];
        elseif ($r['hint_few'] &&  ( ($term->count > 1) &&  ($term->count < 5) )  )
            $hint = $r['hint_few'];
        else
            $hint = $r['hint'];
       $arr['hint'] = esc_attr( $this->str_replace_var($arr, $hint) ) ;
    }else
      $arr['hint']  = esc_attr(strip_tags($term->description));
// установка размера шрифта для каждой ссылки, set the font size for each link
    if($term->font_size)
        $arr['style']  = "font-size:$term->font_size$unit;";
    else
        $arr['style'] = '';
// кол-во записей в таксономии, the number of entries in the taxonomy
    if ($term->count AND $r['show_count'])
       $arr['с_tmpl'] =  $this->str_replace_var($arr, $r['с_tmpl']);
    else
        $arr['с_tmpl'] =  '';

// ну а если у елемента есть потомки начинаем все сначала
//if there are descendants elementa start all over again start method level ()
    if ($term->childs)
        $arr['childs'] = $this->level($term->childs, $depth + 1, $r);
    else
         $arr['childs'] = '';
// окончательная сборка элемента, final assembly member
     $out = $this->str_replace_var($arr, $r['el_tmpl']);
     return $out;
  }
}