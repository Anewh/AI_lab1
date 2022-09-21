<?php 

$read = fopen('bk.txt', 'r');
$strs=array();
$terms=array();
$questions=array();

while(!feof($read)){
    $str=trim(fgets($read), " \n\r");
    $results=explode("ТО", $str)[1];
    array_push($strs, $str);
    preg_match_all('/\(.*?\)/', $str, $tmp);
    foreach($tmp[0] as &$el){
        $el=trim($el, "()");
        $key_ans=explode('=',$el);
        if(!array_key_exists($key_ans[0], $questions)){
            $questions[$key_ans[0]]=array();
            array_push($questions[$key_ans[0]],$key_ans[1]);
        }
        else if(!in_array($key_ans[1], $questions[$key_ans[0]])){
            array_push($questions[$key_ans[0]], $key_ans[1]);
        }
    }
    array_push($tmp, $results);
    array_push($terms, $tmp);
}

function output($iter, $question, $answers){
    echo "<label>".$question."</label> ";
    echo "<select class='form-select' style='max-width: 400px; margin-left: 350px;' name='question".$iter."'>";
    echo "<option> - </option>";
    foreach($answers as $answer){
        echo "<option>".$answer."</option>";
    }
    echo "</select><br><br>";
}

echo "<!DOCTYPE html><html lang='en'>";
echo "<head> <link rel=\"canonical\" href=\"https://getbootstrap.com/docs/5.2/examples/sign-in/\">  </head> <link href=\"https://getbootstrap.com/docs/5.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT\" crossorigin=\"anonymous\">" ;
echo "<form name='question_form' class='form text-center container' method='POST'>";

echo "<div class='text-center'>";
echo '<h1 class="mt-5"> Выбор квартиры </h1>';
echo "Выберите варианты ответов, которые соответствуют вашим предпочтениям при поиске квартиры. В результате будет представлен список вариантов с вероятность, согласно которой вам подходит тот или иной вариант<br><br><br>";

$count=0;
foreach($questions as $question=>$answers){
    output($count, $question,$answers);
    $count++;
}
$keys=array_keys($questions);
echo "<button class=\"w-2 btn btn-lg btn-primary\">Отправить</button>
    </form><br><br>";
$answers=array();
for($i=0;$i<$count;$i++){
    if(isset($_POST['question'.$i])){
        $answers[$keys[$i]]=trim($keys[$i], " ")."=".$_POST['question'.$i];
    }
}
$result=array(); $match=true;
foreach($terms as $term){
    foreach($term[0] as $el){
        if(!in_array($el, $answers)){
            $match=false;
            break;
        }
    }
    if($match==true){
        if(in_array(trim(explode("=",$term[1])[0], " "), $keys, true)){
            $answers[trim(explode("=",$term[1])[0], " ")]=trim($term[1], " ");
        }
            array_push($result, $term[1]);
    }
    $match=true;
}

function getFileResults(string $filename){
    $file = fopen($filename, 'r');
    $results = array();
    while(!feof($file)){
        $line = explode(":", trim(fgets($file), " \n\r"));
        $result = array();
        $result[0] = $line[0];
        $result[1] = explode(",", $line[1]);
        array_push($results, $result);
    }
    return $results;
}

function echoResults($finalResults){
    foreach($finalResults as $elem){
        echo("<div class='lead' >" . $elem[0] . ": вероятность " . $elem[2] . "% <br> </div>");
    }
    echo"<br><br><br><br>";
}

function output_p($result){
    echo "<div class='text-center'>";
    echo '<h1 class="mt-5"> Результат </h1>';
    $expected = getFileResults("results.txt");
    foreach($expected as &$elem){
        $count = 0;
        for($i=0; $i<count($result); $i++){
            if(in_array(substr($result[$i], 1), $elem[1])){
                $count++;
            }
        }
        $elem[2] = ((float)$count)/((float)count($elem[1]));
    }
    echoResults($expected);
    echo "</div>";
}

output_p($result);