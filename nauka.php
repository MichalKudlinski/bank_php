<?php echo  'hello';
// dictionaries

$dict = [
    'key1' => 'value1',
    'key2' => 'value2'

];

echo $dict['key1'];

// jesli mamy dict z 2 wymiarami to najiperw [0] (jesli chcemy element pierwszy) i potem nazwa klucza

// date
$t = date("j "); //zwraca dzisiejszy miesiac

echo $t;


// if statements
if($t < 12){
    echo 'it is not 12th of june yet';

}else{
    echo 'it is past 12th june';
}

//conditionals
$posts = ['First Post'];
echo !empty($posts) ? $posts[0] : 'No posts'; // this means  ? -than : - else

$firstPost = !empty($posts) ? $posts[0] : 'No posts';

echo $firstPost;

// switches

$favcolor = 'red';


switch($favcolor) {
    case 'red':
        echo 'Your favourite color is red';
        break;
    case 'blue':
        echo 'Your favourite color is blue';
        break;
    case 'green':
        echo 'Your favourite color is green';
        break;
    default:
        echo 'You dont have a favourite color';

}

// loops
//for loop

for ($x = 0; $x<= 10; $x++){ // tutaj zamiast 10 moze byc np funkcja count(array), ktora oblicza ilosc wartosci w tablicy
    echo 'Number ' . $x . '<br>';
}


// while
while ($x <= 15){
    echo 'Number' . $x . '<br>';
    $x = $x +1;
}


//foreach
$links = ['First link', 'Second link', 'Third link'];
foreach ($links as $link){
    echo $link;
}

//foreach with dict

$person = [
    'first_name' => 'Brad',
    'last_name' => 'Traversy',
    'email'=> 'brag@gmail.com',

];

foreach($person as $key => $value){
    echo "$key - $value<br>";
}

// funkcje

function registeredUser($email){
    echo $email . ' registered';
}
registeredUser('Brad');

// array functions
//echo count(array)


$fruits = ['apple','orange','pear'];

echo in_array('apple', $fruits);

$fruits[] = 'grape';

print_r($fruits);

array_push($fruits, 'strawberry', 'blueberry'); // dodawanie na koncu

array_unshift($fruits, 'ananas'); // dodawanie na poczatku

print_r($fruits);

//Remove from array

array_pop($fruits);
print_r($fruits);
array_shift($fruits);
print_r($fruits);

// split into chunk - array_chunk(array_name, number_of_chunks)

//concat arrays merged_array = array_merge(arr1,arr2)

// combine array into dictionary : array_combine(arr1,arr2) arr1 will be keys, arr2 will be values

// getting keys from dict array_keys(arr)

//array_flip(arr) - flips keys and values in arr

// range(1,20) numbers from 1 to 19

//array_map(function,array)

//super globals

echo $_SERVER['HTTP_HOST'];
// 'PHP_SELF' , 'SERVER_SOFTWARE', 'DOCUMENT_ROOT', 'SERVER_PORT'



// $_GET with forms only in search bar

// $_POST with all forms